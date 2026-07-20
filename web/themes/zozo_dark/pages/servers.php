<?php
/*
 * ZoZo Dark — servers body override (RB-1 fix).
 *
 * The fork's stock pages/servers.php is a SERVER-DETAIL page: it requires a
 * numeric ?server_id and calls error('invalid server id') + die() without
 * one. The app-shell sidebar "Servers" nav links to ?mode=servers with no
 * id, so the section always errored. This override supplies the missing
 * LIST: with no server_id it renders a DS list of all servers for the game
 * (same treatment as the overview's server panel, each row linking to
 * ?mode=servers&server_id=N); with a server_id it hands off BYTE-FOR-BYTE to
 * the stock detail page, so per-server stats inherit any upstream change.
 */

    if (!defined('IN_HLSTATS')) {
        die('Do not access this file directly.');
    }

    // Detail view: delegate to the stock page unchanged (byte-parity).
    if (isset($_GET['server_id']) && is_numeric($_GET['server_id'])) {
        include (PAGE_PATH . '/servers.php');
        return;
    }

    // List view (no server_id): DS list of all servers for this game.
    require (PAGE_PATH . '/livestats.php');
    $db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
    if ($db->num_rows() < 1) {
        error(__f('common.err.no_such_game', $game));
    }
    list($gamename) = $db->fetch_row();
    $db->free_result();

    pageHeader(array($gamename), array($gamename => ''));

    $nf = function ($n) { return number_format((int) $n, 0, '.', ' '); };

    $db->query("
        SELECT
            serverId, name,
            IF(publicaddress != '', publicaddress, concat(address, ':', port)) AS addr,
            kills, act_players, max_players, act_map
        FROM hlstats_Servers
        WHERE game='$game'
        ORDER BY sortorder, name, serverId
    ");
    $servers = $db->fetch_row_set();
    $total_servers = count($servers);
?>
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
