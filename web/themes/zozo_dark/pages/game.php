<?php
/*
 * ZoZo Dark — game (overview) body, redesign of MOCKUP 01.
 *
 * Theme page-body override for mode=contents -> game.php, resolved via
 * theme()->pagePath('game'). Reuses the stock data queries verbatim
 * (same $total_players / $total_kills / $total_servers / $servers as
 * pages/game.php) and only re-lays the presentation: KPI cards, server
 * fill bars + steam://connect buttons, top-players list, DS chart
 * placeholder. Data logic stays identical to stock so it inherits any
 * upstream query change through a re-sync of this copy (see PLAN §5).
 */

    if (!defined('IN_HLSTATS')) {
        die('Do not access this file directly.');
    }

	require (PAGE_PATH . '/livestats.php');
	$db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
	if ($db->num_rows() < 1) {
		error(__f('common.err.no_such_game', $game));
	}
	list($gamename) = $db->fetch_row();
	$db->free_result();

	pageHeader(array($gamename), array($gamename => ''));

	include (PAGE_PATH . '/voicecomm_serverlist.php');

	// --- Stock KPI queries (kept verbatim) ---
	$result = $db->query("SELECT count(*) FROM hlstats_Players WHERE game='$game'");
	list($total_players) = $db->fetch_row($result);

	$result = $db->query("SELECT players FROM hlstats_Trend WHERE game='$game' AND timestamp<=" . (time() - 86400) . " ORDER BY timestamp DESC LIMIT 0,1");
	list($total_players_24h) = $db->fetch_row($result);
	$players_last_day = -1;
	if ($total_players_24h > 0) {
		$players_last_day = $total_players - $total_players_24h;
	}

	$result = $db->query("SELECT SUM(kills), SUM(headshots), count(serverId) FROM hlstats_Servers WHERE game='$game'");
	list($total_kills, $total_headshots, $total_servers) = $db->fetch_row($result);

	$result = $db->query("SELECT kills FROM hlstats_Trend WHERE game='$game' AND timestamp<=" . (time() - 86400) . " ORDER BY timestamp DESC LIMIT 0,1");
	list($total_kills_24h) = $db->fetch_row($result);
	$db->free_result();

	$query = "
			SELECT
				serverId, name,
				IF(publicaddress != '', publicaddress, concat(address, ':', port)) AS addr,
				kills, headshots, act_players, max_players, act_map, map_started
			FROM hlstats_Servers
			WHERE game='$game'
			ORDER BY sortorder, name, serverId
	";
	$db->query($query);
	$servers = $db->fetch_row_set();
	$db->free_result();

	// --- Online roll-ups (derived from the server set) ---
	$players_online = 0;
	$players_capacity = 0;
	$servers_online = 0;
	foreach ($servers as $s) {
		$players_online   += (int) $s['act_players'];
		$players_capacity += (int) $s['max_players'];
		if ((int) $s['act_players'] > 0) {
			$servers_online++;
		}
	}
	$hpk = ($total_kills > 0) ? sprintf('%.2f', ($total_headshots / $total_kills) * 100) : '0.00';

	// --- KPI subtext data: maps in rotation + 24h online peak ---
	$r = $db->query("SELECT COUNT(DISTINCT act_map) FROM hlstats_Servers WHERE game='$game' AND act_map != ''");
	list($maps_rotation) = $db->fetch_row($r);
	$r = $db->query("SELECT MAX(players) FROM hlstats_Trend WHERE game='$game' AND timestamp >= " . (time() - 86400));
	list($peak_24h) = $db->fetch_row($r);

	// --- Top players (leaderboard preview) ---
	$topPlayers = array();
	$r = $db->query("
		SELECT playerId, lastName, skill, flag
		FROM hlstats_Players
		WHERE game='$game' AND hideranking = 0
		ORDER BY skill DESC
		LIMIT 8
	");
	$topPlayers = $db->fetch_row_set();
	$db->free_result();

	// Short-form big integers for the KPI value (e.g. 181 085 598 -> 181,08М).
	$kfmt = function ($n) {
		$n = (float) $n;
		if ($n >= 1000000) {
			return number_format($n / 1000000, 2, ',', ' ') . 'М';
		}
		if ($n >= 1000) {
			return number_format($n / 1000, 1, ',', ' ') . 'К';
		}
		return number_format($n, 0, '.', ' ');
	};
	$nf = function ($n) { return number_format((int) $n, 0, '.', ' '); };

	// deterministic avatar tint from the player name
	$avatarTint = function ($seed) {
		$palette = array('#5a845a', '#8e44ad', '#27ae60', '#3498db', '#d63031', '#e8b931', '#14b8a6', '#6366f1');
		$h = 0;
		$seed = (string) $seed;
		for ($i = 0; $i < strlen($seed); $i++) { $h = (ord($seed[$i]) + ($h << 5) - $h) & 0xffffff; }
		return $palette[$h % count($palette)];
	};
?>
<div class="page-head">
	<h1><?=__('common.nav.contents')?> — <?php echo htmlspecialchars($gamename); ?></h1>
	<p class="sub"><?php echo $nf($total_players); ?> <?=__('common.nav.players')?>
		<span class="dot">·</span> <?php echo $nf($total_servers); ?> <?=__('common.nav.servers')?>
		<span class="dot">·</span> <span class="live"><?php echo $nf($players_online); ?> <?=__('common.label.online')?></span></p>
</div>

<!-- KPI cards -->
<section class="stats">
	<div class="stat-card">
		<div class="stat-head">
			<span class="stat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0"/><path d="M16 8.5a3 3 0 0 1 0 5"/></svg></span>
			<span class="stat-label"><?=__('game.kpi.players_in_base')?></span>
		</div>
		<div class="stat-val s-blue"><?php echo $nf($total_players); ?></div>
		<div class="stat-sub"><?php if ($players_last_day > -1) { echo sprintf(__('game.kpi.sub.new_24h'), '<b>+' . $nf($players_last_day) . '</b>'); } ?></div>
	</div>
	<div class="stat-card">
		<div class="stat-head">
			<span class="stat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 3l7 7-4 1-1 4-7-7z"/><path d="M9 12l-5 5"/><path d="M4 14v5h5"/></svg></span>
			<span class="stat-label"><?=__('game.kpi.total_kills')?></span>
		</div>
		<div class="stat-val s-red"><?php echo $kfmt($total_kills); ?></div>
		<div class="stat-sub"><?php echo $nf($total_kills); ?> · HS <?php echo $hpk; ?>%</div>
	</div>
	<div class="stat-card">
		<div class="stat-head">
			<span class="stat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="1"/></svg></span>
			<span class="stat-label"><?=__('game.kpi.servers_online')?></span>
		</div>
		<div class="stat-val s-green"><?php echo $nf($servers_online); ?> <span style="font-size:1rem;color:var(--text-faint)">/ <?php echo $nf($total_servers); ?></span></div>
		<div class="stat-sub"><?php echo sprintf(__('game.kpi.sub.maps_rotation'), $nf($maps_rotation)); ?></div>
	</div>
	<div class="stat-card">
		<div class="stat-head">
			<span class="stat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="7" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg></span>
			<span class="stat-label"><?=__('game.kpi.players_online')?></span>
		</div>
		<div class="stat-val s-orange"><?php echo $nf($players_online); ?> <span style="font-size:1rem;color:var(--text-faint)">/ <?php echo $nf($players_capacity); ?></span></div>
		<div class="stat-sub"><?php echo sprintf(__('game.kpi.sub.peak_24h'), $nf($peak_24h)); ?></div>
	</div>
</section>

<!-- Servers panel -->
<section class="panel">
	<div class="panel-head">
		<span class="ptitle"><?=__('game.title.participating_servers')?></span>
		<span class="live"><span class="ld"></span>Live</span>
		<span class="pmeta"><?php echo $nf($total_servers); ?></span>
	</div>
	<table>
		<thead>
			<tr>
				<th>#</th><th></th>
				<th><?=__('servers.col.server')?></th>
				<th><?=__('servers.col.address')?></th>
				<th><?=__('maps.col.map')?></th>
				<th><?=__('game.col.players')?></th>
				<th class="r"><?=__('weapons.col.kills')?></th>
				<th class="r"><?=__('game.col.connect')?></th>
			</tr>
		</thead>
		<tbody>
<?php
	$i = 0;
	foreach ($servers as $s) {
		$i++;
		$addr = $s['addr'];
		$act = (int) $s['act_players'];
		$max = (int) $s['max_players'];
		$pct = ($max > 0) ? min(100, round($act / $max * 100, 1)) : 0;
		$isOn = $act > 0;
?>
			<tr>
				<td class="idx"><?php echo sprintf('%02d', $i); ?></td>
				<td><span class="sdot <?php echo $isOn ? 'on' : 'off'; ?>"></span></td>
				<td class="srv"><a href="<?php echo $g_options['scripturl']; ?>?mode=servers&amp;server_id=<?php echo $s['serverId']; ?>&amp;game=<?php echo $game; ?>"><?php echo htmlspecialchars($s['name']); ?></a></td>
				<td class="addr"><?php echo htmlspecialchars($addr); ?></td>
				<td class="map"><?php echo htmlspecialchars($s['act_map']); ?></td>
				<td><div class="pcell"><div class="pbar"><div class="pfill" style="width:<?php echo $pct; ?>%"></div></div><span class="pcount"><?php echo $act; ?> <span class="den">/ <?php echo $max; ?></span></span></div></td>
				<td class="num r"><?php echo $nf($s['kills']); ?></td>
				<td class="r"><a class="connect" href="steam://connect/<?php echo htmlspecialchars($addr); ?>"><?=__('common.label.play')?></a></td>
			</tr>
<?php } ?>
		</tbody>
	</table>
</section>

<!-- Two-column: activity chart (DS placeholder) + top players -->
<div class="grid2">
	<section class="panel">
		<div class="panel-head">
			<span class="ptitle"><?=__('game.range.24h')?></span>
			<span class="pmeta"><?=__('common.label.online')?></span>
		</div>
		<div class="chart-wrap">
			<div class="chartbox">
<?php if ($g_options['show_server_load_image'] == 1) { ?>
				<img src="show_graph.php?type=1&amp;game=<?php echo $game; ?>&amp;width=600&amp;height=230&amp;bgcolor=<?php echo $g_options['graphbg_load']; ?>&amp;color=<?php echo $g_options['graphtxt_load']; ?>" alt="Server Load Graph" style="width:100%;height:100%;object-fit:cover" />
<?php } else { ?>
				<svg viewBox="0 0 600 230" preserveAspectRatio="none" aria-hidden="true">
					<defs><linearGradient id="ar" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#d63031" stop-opacity=".35"/><stop offset="1" stop-color="#d63031" stop-opacity="0"/></linearGradient></defs>
					<path d="M0,190 L100,182 L200,120 L300,95 L400,88 L500,72 L600,60 L600,230 L0,230 Z" fill="url(#ar)"/>
					<path d="M0,190 L100,182 L200,120 L300,95 L400,88 L500,72 L600,60" fill="none" stroke="#d63031" stroke-width="2"/>
				</svg>
				<span class="chart-note">preview</span>
<?php } ?>
			</div>
		</div>
	</section>

	<section class="panel">
		<div class="panel-head">
			<span class="ptitle"><?=__('game.panel.top_players')?></span>
			<a class="plink" href="<?php echo $g_options['scripturl']; ?>?mode=players&amp;game=<?php echo $game; ?>"><?=__('game.link.full_ranking')?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
		</div>
		<div class="tp-list">
<?php
	$rank = 0;
	foreach ($topPlayers as $p) {
		$rank++;
		$nm = $p['lastName'];
		$initial = mb_strtoupper(mb_substr($nm, 0, 1, 'UTF-8'), 'UTF-8');
?>
			<a class="tp-row" href="<?php echo $g_options['scripturl']; ?>?mode=playerinfo&amp;player=<?php echo $p['playerId']; ?>">
				<span class="tp-rank"><?php echo $rank; ?></span>
				<span class="avatar" style="background:<?php echo $avatarTint($nm); ?>"><?php echo htmlspecialchars($initial); ?></span>
				<span class="tp-name"><span class="nm"><?php echo htmlspecialchars($nm); ?></span></span>
				<span class="tp-pts"><?php echo $nf($p['skill']); ?></span>
			</a>
<?php } ?>
		</div>
	</section>
</div>
