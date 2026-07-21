<?php
/*
 * FEAT-0030 PR-2 — ELO leaderboard cron sync.
 *
 * Pulls the skill_rating public leaderboard (calibrated set, ~1k rows) into the
 * local hlstats_PlayerEloCache so the players leaderboard can render/sort ELO by
 * a local JOIN, with zero live calls per row. Run on the WEB host every 30 min:
 *
 *   * / 30 * * * *  php /path/to/web/cron/elo_sync.php   (or docker exec hlx-web ...)
 *
 * Read-only against skill (public, key-less endpoint). Writes only the local
 * cache table (terminal sink, red line §1). All skill/cache logic lives in
 * EloClient; this wrapper only bootstraps $db and calls zozo_elo_cron_sync().
 */

declare(strict_types=1);

// Must be defined before config.php — it (and the theme includes) guard on it.
if (!defined('IN_HLSTATS')) define('IN_HLSTATS', true);

chdir(__DIR__ . '/..');            // web/ docroot, so config.php resolves like the app
require 'config.php';
require INCLUDE_PATH . '/class_db.php';

$db_classname = 'DB_' . DB_TYPE;
if (!class_exists($db_classname)) {
    fwrite(STDERR, "[elo-sync] DB class $db_classname missing (check config.php DB_TYPE)\n");
    exit(2);
}
$db = new $db_classname(DB_ADDR, DB_USER, DB_PASS, DB_NAME, DB_PCONNECT);

require __DIR__ . '/../themes/zozo_dark/pages/_elo_client.php';

$t0 = microtime(true);
$stats = zozo_elo_cron_sync($db, function ($m) { fwrite(STDERR, "[elo-sync] $m\n"); });
$dt = round(microtime(true) - $t0, 1);

fwrite(STDOUT, sprintf(
    "[elo-sync] done in %ss: total=%d pages=%d rows=%d upserted=%d errors=%d\n",
    $dt, $stats['total'], $stats['pages'], $stats['rows'], $stats['upserted'], $stats['errors']
));

// Non-zero exit only when nothing was written at all (so cron alerts on a real outage,
// not on a few transient row errors).
exit($stats['upserted'] === 0 ? 1 : 0);
