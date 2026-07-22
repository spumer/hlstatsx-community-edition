<?php
/*
 * ZoZo Dark — EloClient (FEAT-0030 PR-1 pilot).
 *
 * Anti-corruption layer between hlstatsx (stats.zozo.gg) and the skill_rating
 * ELO service (skill.zozo.gg). This is the ONLY code in hlstatsx that knows
 * about skill_rating. Everything else reads the local cache table only.
 *
 * Contract (verified PR-0): skill exposes PUBLIC, key-less, server-cached
 * endpoints. GET /public/player/{Y:Z} returns combined_rating, surv/inf,
 * calibrating, rank/rank_total, games_played (404 => no ELO). hlstats uniqueId
 * is already stored as Y:Z, so it maps to the skill key unchanged.
 *
 * RED LINE (FEAT-0030 §1): ELO is a display-only axis. hlstats_PlayerEloCache
 * is a TERMINAL SINK — read only by theme render templates, never joined into
 * votekick / admin-group / rank / Top-40 logic. This file + the theme page are
 * the only places it appears (review grep-gate).
 *
 * Fail-open everywhere (R1, empty-tabs incident): ELO must NEVER block or break
 * the profile render. Any error (service down, timeout, bad JSON, DB) returns
 * null and the badge silently degrades to "—".
 */

if (!defined('IN_HLSTATS')) {
    die('Do not access this file directly.');
}

if (!defined('ZOZO_ELO_API_BASE')) {
    // Public HTTPS origin of the skill_rating SPA (same origin serves /public/*).
    $__eloBase = getenv('ZOZO_ELO_API_BASE');
    define('ZOZO_ELO_API_BASE', $__eloBase !== false && $__eloBase !== '' ? $__eloBase : 'https://skill.zozo.gg');
}
if (!defined('ZOZO_ELO_TTL'))     define('ZOZO_ELO_TTL', 1800);   // cache-row freshness, 30 min
if (!defined('ZOZO_ELO_TIMEOUT')) define('ZOZO_ELO_TIMEOUT', 3);  // total curl timeout, seconds
if (!defined('ZOZO_ELO_MIN_GAMES')) define('ZOZO_ELO_MIN_GAMES', 50); // = skill placement_games (calibrated set)

if (!function_exists('zozo_elo_normalize')) {

    /** hlstats uniqueId -> skill Y:Z key (mirror of skill rating.py normalize_steam_id). */
    function zozo_elo_normalize(string $uniqueId): ?string
    {
        $s = trim($uniqueId);
        if ($s === '') return null;
        if (stripos($s, 'STEAM_') === 0) {
            $p = explode(':', $s, 3);            // ["STEAM_X","Y","Z"]
            if (count($p) === 3) return $p[1] . ':' . $p[2];
            return null;
        }
        $p = explode(':', $s);
        if (count($p) === 3) return $p[1] . ':' . $p[2];  // X:Y:Z -> Y:Z
        if (count($p) === 2) return $s;                    // Y:Z already
        return null;
    }

    /** combined-ELO -> tier name (skill /public/stats boundaries). */
    function zozo_elo_tier(?int $combined): string
    {
        if ($combined === null) return '';
        if ($combined >= 1850) return 'Tank';
        if ($combined >= 1700) return 'Witch';
        if ($combined >= 1550) return 'Hunter';
        if ($combined >= 1400) return 'Charger';
        if ($combined >= 1250) return 'Smoker';
        return 'Common';
    }

    /** tier name -> 1..6 glyph index (reuse the theme's 6 tier masks). */
    function zozo_elo_tier_glyph(string $tier): int
    {
        static $g = ['Common' => 1, 'Smoker' => 2, 'Charger' => 3, 'Hunter' => 4, 'Witch' => 5, 'Tank' => 6];
        return $g[$tier] ?? 1;
    }

    /** Create the terminal-sink cache table once per request (idempotent, no ALTER). */
    function zozo_elo_ensure_table($db): bool
    {
        static $done = null;
        if ($done !== null) return $done;
        try {
            $db->query(
                "CREATE TABLE IF NOT EXISTS hlstats_PlayerEloCache (
                    uid_norm     VARCHAR(32) NOT NULL PRIMARY KEY,
                    combined_elo INT NULL,
                    surv_elo     INT NULL,
                    inf_elo      INT NULL,
                    tier         VARCHAR(16) NULL,
                    rank_pos     INT NULL,
                    rank_total   INT NULL,
                    calibrating  TINYINT NOT NULL DEFAULT 1,
                    games_played INT NULL,
                    fetched_at   INT NOT NULL,
                    INDEX idx_combined (combined_elo)
                ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
                false
            );
            $done = true;
        } catch (\Throwable $e) {
            $done = false; // fail-open: no cache table (e.g. no CREATE priv) -> live-only, still degrades to "—"
        }
        return $done;
    }

    /**
     * ELO snapshot for a hlstats uniqueId, or null when there is no ELO / on any error.
     * Reads the local cache first (TTL); on miss/stale does one live fetch and upserts.
     * Return shape: ['has_elo'=>bool,'combined'=>?int,'surv'=>?int,'inf'=>?int,
     *   'tier'=>string,'glyph'=>int,'rank'=>?int,'rank_total'=>?int,
     *   'calibrating'=>bool,'games_played'=>?int]  (has_elo=false => show "—").
     */
    function zozo_elo_get($db, string $uniqueId): ?array
    {
        $uid = zozo_elo_normalize($uniqueId);
        if ($uid === null) return null;
        $uidSafe = preg_replace('/[^0-9:]/', '', $uid);   // Y:Z is digits+colon only
        if ($uidSafe === '') return null;

        zozo_elo_ensure_table($db);
        $now = time();

        // 1) cache read
        try {
            $r = $db->query("SELECT combined_elo, surv_elo, inf_elo, tier, rank_pos, rank_total, calibrating, games_played, fetched_at
                             FROM hlstats_PlayerEloCache WHERE uid_norm='$uidSafe' LIMIT 1", false);
            if ($r && $db->num_rows($r) > 0) {
                $row = $db->fetch_array($r);
                if ((int) $row['fetched_at'] + ZOZO_ELO_TTL > $now) {
                    return zozo_elo_row_to_view($row);
                }
            }
        } catch (\Throwable $e) { /* fall through to live fetch */ }

        // 2) live fetch (fail-open)
        $data = zozo_elo_fetch($uidSafe);   // ['combined'=>int,...] | 'none' (404) | null (error)
        if ($data === null) {
            return null;  // transient error: no write, degrade to "—" this render
        }

        // 3) upsert cache (negative cache for "none" so we don't re-hit the service)
        if ($data === 'none') {
            $vals = "'$uidSafe', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, $now";
        } else {
            $c = $data['combined']; $s = $data['surv']; $i = $data['inf'];
            $tier = zozo_elo_tier($c);
            $rp = $data['rank'] === null ? 'NULL' : (int) $data['rank'];
            $rt = $data['rank_total'] === null ? 'NULL' : (int) $data['rank_total'];
            $cal = $data['calibrating'] ? 1 : 0;
            $gp = $data['games_played'] === null ? 'NULL' : (int) $data['games_played'];
            $vals = "'$uidSafe', " . (int) $c . ", " . (int) $s . ", " . (int) $i . ", '$tier', $rp, $rt, $cal, $gp, $now";
        }
        try {
            $db->query("INSERT INTO hlstats_PlayerEloCache
                (uid_norm, combined_elo, surv_elo, inf_elo, tier, rank_pos, rank_total, calibrating, games_played, fetched_at)
                VALUES ($vals)
                ON DUPLICATE KEY UPDATE combined_elo=VALUES(combined_elo), surv_elo=VALUES(surv_elo),
                    inf_elo=VALUES(inf_elo), tier=VALUES(tier), rank_pos=VALUES(rank_pos),
                    rank_total=VALUES(rank_total), calibrating=VALUES(calibrating),
                    games_played=VALUES(games_played), fetched_at=VALUES(fetched_at)", false);
        } catch (\Throwable $e) { /* cache write best-effort */ }

        if ($data === 'none') return zozo_elo_row_to_view(['combined_elo' => null]);
        return zozo_elo_row_to_view([
            'combined_elo' => $data['combined'], 'surv_elo' => $data['surv'], 'inf_elo' => $data['inf'],
            'tier' => zozo_elo_tier((int) $data['combined']), 'rank_pos' => $data['rank'],
            'rank_total' => $data['rank_total'], 'calibrating' => $data['calibrating'] ? 1 : 0,
            'games_played' => $data['games_played'],
        ]);
    }

    /** Map a cache row (or synthetic array) to the render view. */
    function zozo_elo_row_to_view(array $row): array
    {
        $combined = isset($row['combined_elo']) && $row['combined_elo'] !== null ? (int) $row['combined_elo'] : null;
        if ($combined === null) {
            return ['has_elo' => false, 'combined' => null, 'surv' => null, 'inf' => null,
                    'tier' => '', 'glyph' => 0, 'rank' => null, 'rank_total' => null,
                    'calibrating' => false, 'games_played' => null];
        }
        $tier = $row['tier'] ?? zozo_elo_tier($combined);
        return [
            'has_elo'      => true,
            'combined'     => $combined,
            'surv'         => isset($row['surv_elo']) ? (int) $row['surv_elo'] : null,
            'inf'          => isset($row['inf_elo']) ? (int) $row['inf_elo'] : null,
            'tier'         => $tier,
            'glyph'        => zozo_elo_tier_glyph($tier),
            'rank'         => isset($row['rank_pos']) && $row['rank_pos'] !== null ? (int) $row['rank_pos'] : null,
            'rank_total'   => isset($row['rank_total']) && $row['rank_total'] !== null ? (int) $row['rank_total'] : null,
            'calibrating'  => !empty($row['calibrating']),
            'games_played' => isset($row['games_played']) && $row['games_played'] !== null ? (int) $row['games_played'] : null,
        ];
    }

    /**
     * One live GET /public/player/{Y:Z}. Returns:
     *   array  — decoded rating fields, on 200
     *   'none' — on 404 (player has no ELO)
     *   null   — on any transport/parse error (caller degrades, no cache write)
     */
    function zozo_elo_fetch(string $uid)
    {
        $url = rtrim(ZOZO_ELO_API_BASE, '/') . '/public/player/' . rawurlencode($uid);
        if (!function_exists('curl_init')) return null;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => ZOZO_ELO_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 2,
            CURLOPT_USERAGENT      => 'hlstatsx-zozo-elo/1.0',
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($code === 404) return 'none';
        if ($code !== 200 || !is_string($body) || $body === '') return null;
        $d = json_decode($body, true);
        if (!is_array($d) || !isset($d['combined_rating'])) return null;
        return [
            'combined'     => (int) round($d['combined_rating']),
            'surv'         => isset($d['surv_rating']) ? (int) round($d['surv_rating']) : null,
            'inf'          => isset($d['inf_rating']) ? (int) round($d['inf_rating']) : null,
            'calibrating'  => !empty($d['calibrating']),
            'rank'         => isset($d['rank']) && $d['rank'] !== null ? (int) $d['rank'] : null,
            'rank_total'   => isset($d['rank_total']) && $d['rank_total'] !== null ? (int) $d['rank_total'] : null,
            'games_played' => isset($d['games_played']) && $d['games_played'] !== null ? (int) $d['games_played'] : null,
        ];
    }

    /**
     * Cache-ONLY ELO lookup for a set of playerIds (leaderboard column, PR-2).
     * No live fetch — the leaderboard is bulk-filled by the cron. Returns
     * [playerId => ['combined'=>int,'tier'=>string,'glyph'=>int,'calibrating'=>bool]]
     * for players that have ELO. Fail-open: any error yields an empty map (-> "—").
     * This reads the terminal-sink table for DISPLAY only; it does not touch the
     * ranking/order SQL, so ELO never influences rank/privilege (red line §1).
     */
    function zozo_elo_batch_by_players($db, array $playerIds): array
    {
        $out = array();
        $ids = array();
        foreach ($playerIds as $p) { $p = (int) $p; if ($p > 0) $ids[$p] = $p; }
        if (empty($ids)) return $out;
        if (!zozo_elo_ensure_table($db)) return $out;
        $list = implode(',', $ids);
        try {
            // JOIN notes (seam-review r1):
            //  D1: prod hlstats_PlayerUniqueIds.uniqueId is utf8mb4_unicode_ci while the cache is
            //      general_ci -> two IMPLICIT operands, different collation -> "Illegal mix of
            //      collations" -> fail-open -> the whole column silently "—" on prod. An explicit
            //      COLLATE on the seam wins (coercibility 0) regardless of the declared collations.
            //  O1: normalize uniqueId to Y:Z in SQL (SUBSTRING_INDEX .. -2) so STEAM_/3-part rows
            //      imported later still match the normalized cache key.
            //  O2: leaderboard shows only calibrated players (plan §5.2) -> calibrating = 0.
            $r = $db->query("SELECT pu.playerId AS pid, ec.combined_elo, ec.tier, ec.calibrating
                             FROM hlstats_PlayerUniqueIds pu
                             JOIN hlstats_PlayerEloCache ec
                               ON ec.uid_norm = SUBSTRING_INDEX(pu.uniqueId, ':', -2) COLLATE utf8mb4_unicode_ci
                             WHERE pu.playerId IN ($list) AND ec.combined_elo IS NOT NULL AND ec.calibrating = 0", false);
            if ($r) {
                while ($row = $db->fetch_array($r)) {
                    $pid = (int) $row['pid'];
                    if (isset($out[$pid])) continue;   // first uniqueId wins
                    $tier = $row['tier'] ?? zozo_elo_tier((int) $row['combined_elo']);
                    $out[$pid] = [
                        'combined'    => (int) $row['combined_elo'],
                        'tier'        => $tier,
                        'glyph'       => zozo_elo_tier_glyph($tier),
                        'calibrating' => !empty($row['calibrating']),
                    ];
                }
            }
        } catch (\Throwable $e) { /* fail-open -> "—" */ }
        return $out;
    }

    /**
     * Bulk pre-fill the cache from skill GET /public/leaderboard (calibrated set,
     * min_games = placement). Paginates (limit 50). Updates ONLY the leaderboard-
     * owned fields (combined_elo/tier/calibrating/games_played/fetched_at); leaves
     * rank_pos/rank_total/surv/inf to the profile live-fill so the "#N of M" on the
     * badge stays profile-consistent. Returns run stats. Run from the cron wrapper.
     */
    function zozo_elo_cron_sync($db, ?callable $log = null): array
    {
        $stats = ['pages' => 0, 'rows' => 0, 'upserted' => 0, 'errors' => 0, 'total' => 0];
        if (!function_exists('curl_init'))   { $stats['errors']++; return $stats; }
        if (!zozo_elo_ensure_table($db))     { $stats['errors']++; return $stats; }
        $base = rtrim(ZOZO_ELO_API_BASE, '/');
        $now = time();
        $limit = 50; $offset = 0; $total = null; $mg = ZOZO_ELO_MIN_GAMES;
        do {
            $url = "$base/public/leaderboard?side=combined&limit=$limit&offset=$offset&min_games=$mg";
            $ch = curl_init($url);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_USERAGENT => 'hlstatsx-zozo-elo-cron/1.0']);
            $body = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch);
            if ($code !== 200 || !is_string($body)) { $stats['errors']++; break; }
            $d = json_decode($body, true);
            if (!is_array($d) || !isset($d['leaderboard'])) { $stats['errors']++; break; }
            if ($total === null) { $total = (int) ($d['total'] ?? 0); $stats['total'] = $total; }
            $batch = $d['leaderboard'];
            if (empty($batch)) break;
            $stats['pages']++;
            foreach ($batch as $e) {
                $uid = zozo_elo_normalize((string) ($e['steam_id'] ?? ''));
                if ($uid === null) { $stats['errors']++; continue; }
                $uidSafe = preg_replace('/[^0-9:]/', '', $uid);
                if ($uidSafe === '' || !isset($e['rating'])) { $stats['errors']++; continue; }
                $combined = (int) round($e['rating']);
                $tier = zozo_elo_tier($combined);
                $gp = isset($e['games_played']) ? (int) $e['games_played'] : 'NULL';
                try {
                    $db->query("INSERT INTO hlstats_PlayerEloCache (uid_norm, combined_elo, tier, calibrating, games_played, fetched_at)
                                VALUES ('$uidSafe', $combined, '$tier', 0, $gp, $now)
                                ON DUPLICATE KEY UPDATE combined_elo=VALUES(combined_elo), tier=VALUES(tier),
                                    calibrating=VALUES(calibrating), games_played=VALUES(games_played), fetched_at=VALUES(fetched_at)", false);
                    $stats['upserted']++;
                } catch (\Throwable $ex) { $stats['errors']++; }
                $stats['rows']++;
            }
            if ($log) $log("offset=$offset got=" . count($batch) . " total=$total");
            $offset += $limit;
        } while ($total !== null && $offset < $total);
        return $stats;
    }
}
