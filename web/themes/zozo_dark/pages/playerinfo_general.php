<?php
/*
 * ZoZo Dark — player profile "general" tab body, redesign of MOCKUP 03
 * (grid2: Информация + Сводная статистика + rank progress).
 *
 * Theme page-body override for the playerinfo general tab, resolved via
 * theme()->pagePath('playerinfo_general') on BOTH the non-JS require_once
 * path and the AJAX tab path (playerinfo.php). Runs in the same scope as
 * the stock file ($playerdata, $player, $game, $statusmsg, $realkills,
 * $realheadshots, $realdeaths, $realteamkills, $pl_urlname, $siteurlneo).
 *
 * ALL stock data queries are reproduced verbatim (drop-in contract) so the
 * numbers stay identical to pages/playerinfo_general.php; only the
 * presentation is re-laid into the DS definition lists + rank card. The
 * forum-signature BB-code block (image + setForumText JS + phpBB/IPB/direct
 * links + siglink textarea) is preserved BYTE-for-BYTE — it is operational
 * legacy the founder asked to keep.
 *
 * NOTE (honesty): visual conformance to mockup 03 is NOT self-certified
 * here; it is subject to the independent VRTF review + a live-stand
 * browser tab-click gate, both pending the stand-free signal.
 */

    if (!defined('IN_HLSTATS')) {
        die('Do not access this file directly.');
    }

    require_once __DIR__ . '/_rank_ru.php';

    $container = require ROOT_PATH . '/bootstrap.php';
    $playerRepo = $container->get(\Repository\PlayerRepository::class);

    // --- Steam identity + live status (stock verbatim) ---
    $db->query("
        SELECT
            hlstats_PlayerUniqueIds.uniqueId,
            CAST(LEFT(hlstats_PlayerUniqueIds.uniqueId,1) AS unsigned) + CAST('76561197960265728' AS unsigned) + CAST(MID(hlstats_PlayerUniqueIds.uniqueId, 3,10)*2 AS unsigned) AS communityId
        FROM hlstats_PlayerUniqueIds
        WHERE hlstats_PlayerUniqueIds.playerId = '$player'
    ");
    list($uqid, $coid) = $db->fetch_row();
    $status = 'Unknown';
    $avatar_full = IMAGE_PATH . "/unknown.jpg";
    $xml = null;
    if ($coid !== '76561197960265728') {
        $profileUrl = "https://steamcommunity.com/profiles/$coid?xml=1";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $profileUrl);
        $xml = curl_exec($curl);
        curl_close($curl);
    }
    $xmlDoc = $xml ? simplexml_load_string($xml) : null;
    if ($xmlDoc) {
        $status = ucwords($xmlDoc->onlineState);
        $avatar_full = $xmlDoc->avatarFull;
    }
    $prefix = ((!preg_match('/^BOT/i', $uqid)) && $g_options['Mode'] == 'Normal') ? 'STEAM_0:' : '';

    // --- Last connect (stock verbatim) ---
    $db->query("SELECT DATE_FORMAT(eventTime, '%a. %b. %D, %Y @ %T') FROM hlstats_Events_Connects WHERE hlstats_Events_Connects.playerId = '$player' ORDER BY id desc LIMIT 1");
    list($lastevent) = $db->fetch_row();

    // --- Average ping (stock verbatim) ---
    $db->query("SELECT ROUND(SUM(hlstats_Events_Latency.ping) / COUNT(hlstats_Events_Latency.ping), 0) AS av_ping, ROUND(ROUND(SUM(hlstats_Events_Latency.ping) / COUNT(ping), 0) / 2, 0) AS av_latency FROM hlstats_Events_Latency WHERE hlstats_Events_Latency.playerId = '$player'");
    list($av_ping, $av_latency) = $db->fetch_row();

    // --- Player rank number (stock repo call) ---
    $rank = __('livestats.msg.unknown');
    if ($playerdata['activity'] > 0 && $playerdata['hideranking'] == 0) {
        $rank = $playerRepo->getPlayerRank($playerdata['game'], $g_options['rankingtype'], $playerdata[$g_options['rankingtype']], $playerdata['kills'], $playerdata['deaths']);
        if (is_null($rank)) { $rank = __('livestats.msg.unknown'); }
    } elseif ($playerdata['hideranking'] == 1) {
        $rank = __('playerinfo_general.rank.hidden');
    } elseif ($playerdata['hideranking'] == 2) {
        $rank = __('playerinfo_general.rank.excluded');
    } else {
        $rank = __('playerinfo_general.rank.not_active');
    }

    // --- Real (28-day event) summary values (stock verbatim) ---
    $db->query("SELECT IFNULL(ROUND(SUM(hlstats_Events_Frags.killerId = '$player') / IF(SUM(hlstats_Events_Frags.victimId = '$player') = 0, 1, SUM(hlstats_Events_Frags.victimId = '$player')), 2), '-') FROM hlstats_Events_Frags WHERE (hlstats_Events_Frags.killerId = '$player' OR hlstats_Events_Frags.victimId = '$player')");
    list($realkpd) = $db->fetch_row();
    $db->query("SELECT IFNULL(SUM(hlstats_Events_Frags.headshot=1) / COUNT(*), '-') FROM hlstats_Events_Frags WHERE hlstats_Events_Frags.killerId = '$player'");
    list($realhpk) = $db->fetch_row();
    $db->query("SELECT IFNULL(ROUND((SUM(hlstats_Events_Statsme.hits) / SUM(hlstats_Events_Statsme.shots) * 100), 2), 0.0) AS accuracy, SUM(hlstats_Events_Statsme.shots) AS shots, SUM(hlstats_Events_Statsme.hits) AS hits, SUM(hlstats_Events_Statsme.kills) AS kills FROM hlstats_Events_Statsme WHERE hlstats_Events_Statsme.playerId='$player'");
    list($sm_accuracy, $sm_shots, $sm_hits, $sm_kills) = $db->fetch_row();
    $shots_per_kill = ($sm_kills > 0) ? sprintf('%.2f', ($sm_shots / $sm_kills)) : '-';
    $kills_per_min = ($playerdata['connection_time'] > 0) ? sprintf('%.2f', ($playerdata['kills'] / ($playerdata['connection_time'] / 60))) : '-';
    $db->query("SELECT hlstats_Players.kill_streak FROM hlstats_Players WHERE hlstats_Players.playerId = '$player'");
    list($kill_streak) = $db->fetch_row();
    $db->query("SELECT hlstats_Players.death_streak FROM hlstats_Players WHERE hlstats_Players.playerId = '$player'");
    list($death_streak) = $db->fetch_row();
    $db->query("SELECT COUNT(*) FROM hlstats_Players_Awards WHERE hlstats_Players_Awards.playerId = $player");
    list($numawards) = $db->fetch_row();
    $headshots_display = ($playerdata['headshots'] == 0) ? $realheadshots : $playerdata['headshots'];

    // --- Rank progress (current + next rank; stock verbatim) ---
    $db->query("SELECT hlstats_Ranks.rankName, hlstats_Ranks.image, hlstats_Ranks.minKills FROM hlstats_Ranks WHERE hlstats_Ranks.minKills <= " . (int) $playerdata['kills'] . " AND hlstats_Ranks.game = '$game' ORDER BY hlstats_Ranks.minKills DESC LIMIT 1");
    $curRank = $db->fetch_array();
    $rankName = $curRank ? zozo_rank_ru($curRank['rankName']) : '';
    $rankTier = zozo_rank_tier((int) $playerdata['kills']);
    $rankCurMinKills = $curRank ? $curRank['minKills'] : 0;
    $db->query("SELECT hlstats_Ranks.rankName, hlstats_Ranks.minKills FROM hlstats_Ranks WHERE hlstats_Ranks.minKills > " . (int) $playerdata['kills'] . " AND hlstats_Ranks.game = '$game' ORDER BY hlstats_Ranks.minKills LIMIT 1");
    if ($db->num_rows() == 0) {
        $nextRankName = '';
        $rankKillsNeeded = 0;
        $rankPercent = 100;
    } else {
        $nextRank = $db->fetch_array();
        $nextRankName = zozo_rank_ru($nextRank['rankName']);
        $rankKillsNeeded = $nextRank['minKills'] - $playerdata['kills'];
        $span = ($nextRank['minKills'] - $rankCurMinKills);
        $rankPercent = $span > 0 ? round(($playerdata['kills'] - $rankCurMinKills) * 100 / $span, 1) : 0;
    }

    // helpers
    $nf = function ($n) { return number_format((int) $n, 0, '.', ' '); };
    $rankDisplay = is_numeric($rank) ? ('#' . $nf($rank)) : $rank;
    $flagImg = '';
    if (!empty($playerdata['flag'])) {
        $flagImg = '<img class="tableicon" src="' . getFlag($playerdata['flag']) . '" alt="' . htmlspecialchars($playerdata['country']) . '" title="' . htmlspecialchars($playerdata['country']) . '" />';
    }
?>
<div class="grid2 profile-grid">
	<!-- Информация -->
	<section class="panel">
		<div class="panel-head"><span class="ptitle"><?=__('playerinfo_general.col.player_profile')?></span></div>
		<div class="dl">
			<div class="r"><span class="k"><?=__('common.col.player')?></span><span class="v"><?php echo $flagImg; ?> <?php echo htmlspecialchars($playerdata['lastName'], ENT_COMPAT); ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.label.location')?></span><span class="v"><?php
				if ($playerdata['country']) { echo ($playerdata['city'] ? htmlspecialchars($playerdata['city'], ENT_COMPAT) . ', ' : '') . '<a class="lnk" href="' . $g_options['scripturl'] . '?mode=countryclansinfo&amp;flag=' . $playerdata['flag'] . "&amp;game=$game\">" . htmlspecialchars($playerdata['country']) . '</a>'; }
				else { echo '<span class="faint">' . __('playerinfo_general.location_unknown') . '</span>'; }
			?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.label.steam')?></span><span class="v mono"><a class="lnk" href="http://steamcommunity.com/profiles/<?php echo $coid; ?>" target="_blank" rel="noopener"><?php echo $prefix . htmlspecialchars($uqid); ?></a></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.label.status')?></span><span class="v"><?php echo htmlspecialchars($status); ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.label.karma')?></span><span class="v"><?php echo $statusmsg; ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.label.member_of_clan')?></span><span class="v"><?php
				if ($playerdata['clan']) { echo '<a class="lnk" href="' . $g_options['scripturl'] . '?mode=claninfo&amp;clan=' . $playerdata['clan'] . '">' . htmlspecialchars($playerdata['clan_name'], ENT_COMPAT) . '</a>'; }
				else { echo '<span class="faint">' . __('playerinfo_general.no_clan') . '</span>'; }
			?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.label.last_connect')?></span><span class="v mono"><?php echo $lastevent ? htmlspecialchars($lastevent) : __('playerinfo_general.msg.unknown_paren'); ?></span></div>
			<div class="r"><span class="k"><?=__('countryclansinfo.row.total_connection_time')?></span><span class="v mono"><?php echo timestamp_to_str($playerdata['connection_time']); ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.label.avg_ping')?></span><span class="v mono"><?php echo $av_ping ? $av_ping . __f('playerinfo_general.label.latency', $av_latency) : '&mdash;'; ?></span></div>
		</div>
	</section>

	<!-- Сводная статистика -->
	<section class="panel">
		<div class="panel-head"><span class="ptitle"><?=__('countryclansinfo.stats_summary')?></span></div>
		<div class="dl">
			<div class="r"><span class="k"><?=__('countryclansinfo.row.activity')?></span><span class="v"><div class="pcell" style="justify-content:flex-end"><div class="pbar" style="flex-basis:80px;width:80px"><div class="pfill" style="width:<?php echo min(100, (int) $playerdata['activity']); ?>%"></div></div><span class="mono"><?php echo (int) $playerdata['activity']; ?>%</span></div></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.points')?></span><span class="v mono link"><?php echo $nf($playerdata['skill']); ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.rank')?></span><span class="v mono"><?php echo htmlspecialchars($rankDisplay); ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.kills_per_minute')?></span><span class="v mono"><?php echo $kills_per_min; ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.kills_per_death')?></span><span class="v mono"><?php echo is_numeric($playerdata['kpd']) ? number_format((float) $playerdata['kpd'], 2) : htmlspecialchars($playerdata['kpd']); ?> <span class="faint">(<?php echo is_numeric($realkpd) ? number_format((float) $realkpd, 2) : htmlspecialchars($realkpd); ?>)</span></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.headshots_per_kill')?></span><span class="v mono"><?php echo is_numeric($playerdata['hpk']) ? number_format((float) $playerdata['hpk'], 2) : htmlspecialchars($playerdata['hpk']); ?> <span class="faint">(<?php echo is_numeric($realhpk) ? number_format((float) $realhpk, 2) : htmlspecialchars($realhpk); ?>)</span></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.shots_per_kill')?></span><span class="v mono"><?php echo $shots_per_kill; ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.weapon_accuracy')?></span><span class="v mono"><?php echo htmlspecialchars($playerdata['acc']); ?>% <span class="faint">(<?php echo sprintf('%.0f', $sm_accuracy); ?>%)</span></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.headshots')?></span><span class="v mono"><?php echo $nf($headshots_display); ?> <span class="faint">(<?php echo $nf($realheadshots); ?>)</span></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.kills')?></span><span class="v mono"><?php echo $nf($playerdata['kills']); ?> <span class="faint">(<?php echo $nf($realkills); ?>)</span></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.deaths')?></span><span class="v mono"><?php echo $nf($playerdata['deaths']); ?> <span class="faint">(<?php echo $nf($realdeaths); ?>)</span></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.longest_kill_streak')?></span><span class="v mono"><?php echo $nf($kill_streak); ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.longest_death_streak')?></span><span class="v mono"><?php echo $nf($death_streak); ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.suicides')?></span><span class="v mono"><?php echo $nf($playerdata['suicides']); ?></span></div>
			<div class="r"><span class="k"><?=__('playerinfo_general.row.teammate_kills')?></span><span class="v mono"><?php echo $nf($playerdata['teamkills']); ?> <span class="faint">(<?php echo $nf($realteamkills); ?>)</span></span></div>
		</div>
	</section>
</div>

<!-- Rank progress -->
<section class="panel">
	<div class="panel-head"><span class="ptitle"><?=__('playerinfo_general.row.rank')?></span></div>
	<div class="rankcard">
		<div class="rank-top">
			<span class="rank-ins"><span class="tier-glyph big t<?php echo $rankTier; ?>" aria-hidden="true"></span></span>
			<div>
				<div class="rank-name"><?php echo $rankName !== '' ? htmlspecialchars($rankName) : '&mdash;'; ?></div>
				<div class="rank-sub"><?php echo $nf($playerdata['kills']); ?> <?=__('playerinfo_general.row.kills')?></div>
			</div>
		</div>
<?php if ($nextRankName !== '') { ?>
		<div class="rank-bar"><div class="rank-fill" style="width:<?php echo max(0, min(100, $rankPercent)); ?>%"></div></div>
		<div class="rank-legend"><span><?php echo htmlspecialchars($rankName); ?></span><span><b><?php echo $nf($rankKillsNeeded); ?></b> &rarr; <?php echo htmlspecialchars($nextRankName); ?></span></div>
<?php } else { ?>
		<div class="rank-bar"><div class="rank-fill" style="width:100%"></div></div>
		<div class="rank-legend"><span><?php echo htmlspecialchars($rankName); ?></span><span class="faint"><?=__('playerinfo.rank.max')?></span></div>
<?php } ?>
	</div>
</section>

<!-- Trend graph + forum signature -->
<div class="grid2 profile-grid">
	<section class="panel">
		<div class="panel-head"><span class="ptitle"><?=__('playerinfo_general.col.player_trend')?></span></div>
		<div class="chart-wrap"><div class="chartbox">
			<img src="trend_graph.php?bgcolor=<?php echo $g_options['graphbg_trend']; ?>&amp;color=<?php echo $g_options['graphtxt_trend']; ?>&amp;player=<?php echo $player; ?>" alt="<?=__('playerinfo_general.alt.trend_graph')?>" style="max-width:100%" />
		</div></div>
	</section>

	<section class="panel">
		<div class="panel-head"><span class="ptitle"><?=__('playerinfo_general.col.forum_signature')?></span></div>
		<div class="sigbox" style="padding:1.1rem;text-align:center;">
<?php
	// Forum signature BB-code — preserved byte-for-byte from stock.
	if ($g_options['modrewrite'] == 0) {
		$imglink = $siteurlneo.'sig.php?player_id='.$player.'&amp;background='.$g_options['sigbackground'];
		$jimglink = $siteurlneo.'sig.php?player_id='.$player.'&background='.$g_options['sigbackground'];
	} else {
		$imglink = $siteurlneo.'sig-'.$player.'-'.$g_options['sigbackground'].'.png';
		$jimglink = $imglink;
	}
	echo "<img src=\"$imglink\" title=\"" . __('playerinfo_general.title.forum_sig') . "\" alt=\"" . __('playerinfo_general.alt.forum_sig_image') . "\"/>";
	$script_path = (isset($_SERVER['SSL']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")) ? 'https://' : 'http://';
	$script_path .= $_SERVER['HTTP_HOST'];
	$script_path .= str_replace('\\','/',dirname($_SERVER['PHP_SELF']));
	$script_path = preg_replace('/\/$/','',$script_path);
?>
			<br /><br />
			<script type="text/javascript">
				/* <![CDATA[ */
				function setForumText(val)
				{
					var txtArea = document.getElementById('siglink');
					switch(val)
					{
						case 0:
							<?php echo "txtArea.value = '$jimglink'\n"; ?>
							break;
						case 1:
							<?php echo "txtArea.value = '[url=$script_path/hlstats.php?mode=playerinfo&player=$player"."][img]$jimglink"."[/img][/url]'\n"; ?>
							break;
						case 2:
							<?php echo "txtArea.value = '[url=\"$script_path/hlstats.php?mode=playerinfo&player=$player\"][img]$jimglink"."[/img][/url]'\n"; ?>
							break;
					}
				}
				/* ]]> */
			</script>
			<a class="lnk" href="" onclick="setForumText(1);return false"><?=__('playerinfo_general.bbcode.phpbb')?></a>&nbsp;|&nbsp;<a class="lnk" href="" onclick="setForumText(2);return false"><?=__('playerinfo_general.bbcode.ipb')?></a>&nbsp;|&nbsp;<a class="lnk" href="" onclick="setForumText(0);return false"><?=__('playerinfo_general.bbcode.direct_image')?></a>
			<?php echo '<textarea style="width: 95%; height: 50px;" rows="2" cols="70" id="siglink" readonly="readonly" onclick="document.getElementById(\'siglink\').select();">[url='."$script_path/hlstats.php?mode=playerinfo&amp;player=$player"."][img]$imglink".'[/img][/url]</textarea>'; ?>
		</div>
	</section>
</div>
