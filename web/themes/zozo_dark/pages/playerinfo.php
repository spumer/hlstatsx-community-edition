<?php
/*
 * ZoZo Dark — player profile body, redesign of MOCKUP 03 (shell).
 *
 * Theme page-body override for mode=playerinfo, resolved via
 * theme()->pagePath('playerinfo'). Reuses the stock player-data queries
 * verbatim and re-lays only the top of the page: hero block (avatar,
 * name, flag, tier badge, SteamID, actions) + 6 stat tiles. The tab
 * shell (ul.subsection_tabs + Tabs JS) and the AJAX branch — including
 * the tab-name sanitizer — are preserved BYTE-for-BYTE from the stock
 * page: that machinery drives the async tab loads and regressing it
 * blanks the profile tabs (incident #18). The tab CSS (red underline)
 * lives in theme.css. General-tab content still loads the stock
 * playerinfo_general.php (dark-repainted) until its own theme copy lands.
 */

    if (!defined('IN_HLSTATS')) {
        die('Do not access this file directly.');
    }

	require_once __DIR__ . '/_rank_ru.php';

	// Player Details
	$player = valid_request(intval($_GET['player'] ?? ''), true);
	$uniqueid = valid_request(strval($_GET['uniqueid'] ?? ''), false);
	$game = valid_request(strval($_GET['game'] ?? ''), false);

	if (!$player && $uniqueid) {
		if (!$game) {
			header("Location: " . $g_options['scripturl'] . "&mode=search&st=uniqueid&q=$uniqueid");
			exit;
		}
		$uniqueid = preg_replace('/^STEAM_\d+?\:/i', '', $uniqueid);
		$db->query("SELECT hlstats_PlayerUniqueIds.playerId FROM hlstats_PlayerUniqueIds WHERE hlstats_PlayerUniqueIds.uniqueId = '$uniqueid'");
		if ($db->num_rows() > 1) {
			header("Location: " . $g_options['scripturl'] . "&mode=search&st=uniqueid&q=$uniqueid&game=$game");
			exit;
		} elseif ($db->num_rows() < 1) {
			error(__f('playerinfo.err.no_players_matching_uniqueid', $uniqueid));
		} else {
			list($player) = $db->fetch_row();
			$player = intval($player);
		}
	} elseif (!$player && !$uniqueid) {
		error(__('playerawards.no_player_id'));
	}

	$db->query("
		SELECT
			hlstats_Players.playerId, hlstats_Players.connection_time,
			unhex(replace(hex(hlstats_Players.lastName), 'E280AE', '')) as lastName,
			hlstats_Players.country, hlstats_Players.city, hlstats_Players.flag,
			hlstats_Players.clan, hlstats_Players.fullName, hlstats_Players.email,
			hlstats_Players.homepage, hlstats_Players.icq, hlstats_Players.mmrank,
			hlstats_Players.game, hlstats_Players.hideranking, hlstats_Players.blockavatar,
			hlstats_Players.skill, hlstats_Players.kills, hlstats_Players.deaths,
			IFNULL(kills / deaths, '-') AS kpd, hlstats_Players.suicides,
			hlstats_Players.headshots, IFNULL(headshots / kills, '-') AS hpk,
			hlstats_Players.shots, hlstats_Players.hits, hlstats_Players.teamkills,
			IFNULL(ROUND((hits / shots * 100), 1), 0) AS acc,
			CONCAT(hlstats_Clans.name) AS clan_name, activity
		FROM hlstats_Players
		LEFT JOIN hlstats_Clans ON hlstats_Clans.clanId = hlstats_Players.clan
		WHERE hlstats_Players.playerId = '$player'
		LIMIT 1
	");
	if ($db->num_rows() != 1) {
		error(__f('common.err.no_such_player', $player));
	}
	$playerdata = $db->fetch_array();
	$db->free_result();
	$pl_name = $playerdata['lastName'];
	if (strlen($pl_name) > 10) {
		$pl_shortname = substr($pl_name, 0, 8) . '...';
	} else {
		$pl_shortname = $pl_name;
	}
	$pl_name = htmlspecialchars($pl_name, ENT_COMPAT);
	$pl_shortname = htmlspecialchars($pl_shortname, ENT_COMPAT);
	$pl_urlname = urlencode($playerdata['lastName']);
	$game = $playerdata['game'];

	$db->query("SELECT hlstats_Games.name FROM hlstats_Games WHERE hlstats_Games.code = '$game'");
	if ($db->num_rows() != 1) {
		$gamename = ucfirst($game);
	} else {
		list($gamename) = $db->fetch_row();
	}

	$hideranking = $playerdata['hideranking'];
	if ($hideranking == 2) {
		$statusmsg = __('playerinfo.status.banned');
	} else {
		$statusmsg = __('playerinfo.status.good_standing');
	}

	$db->query("SELECT COUNT(hlstats_Events_Frags.killerId) FROM hlstats_Events_Frags WHERE hlstats_Events_Frags.killerId = '$player' AND hlstats_Events_Frags.headshot = 1");
	list($realheadshots) = $db->fetch_row();
	$db->query("SELECT COUNT(hlstats_Events_Frags.killerId) FROM hlstats_Events_Frags WHERE hlstats_Events_Frags.killerId = '$player'");
	list($realkills) = $db->fetch_row();
	$db->query("SELECT COUNT(hlstats_Events_Frags.victimId) FROM hlstats_Events_Frags WHERE hlstats_Events_Frags.victimId = '$player'");
	list($realdeaths) = $db->fetch_row();
	$db->query("SELECT COUNT(hlstats_Events_Teamkills.killerId) FROM hlstats_Events_Teamkills WHERE hlstats_Events_Teamkills.killerId = '$player'");
	list($realteamkills) = $db->fetch_row();

	if (!isset($_GET['killLimit'])) {
		$killLimit = 5;
	} else {
		$killLimit = valid_request($_GET['killLimit'], true);
	}

	// AJAX tab branch — preserved BYTE-for-BYTE from stock (sanitizer incl.).
	if (isset($_GET['type']) && $_GET['type'] == 'ajax') {
		$tabs = explode('_', preg_replace('[^a-z]', '', $_GET['tab']));

		foreach ($tabs as $tab) {
			if (file_exists(PAGE_PATH . "/playerinfo_$tab.php")) {
				@include(theme()->pagePath("playerinfo_$tab") ?? (PAGE_PATH . "/playerinfo_$tab.php"));
			}
		}
		exit;
	}

	// --- ZoZo hero data: SteamID + tier (rankName from hlstats_Ranks) ---
	$steamid = '';
	$db->query("SELECT uniqueId FROM hlstats_PlayerUniqueIds WHERE playerId = '$player' LIMIT 1");
	if ($db->num_rows() > 0) {
		list($uid) = $db->fetch_row();
		$steamid = preg_match('/^\d+:\d+:\d+$/', $uid) ? 'STEAM_' . $uid : $uid;
	}
	$rankName = '';
	$rr = $db->query("SELECT rankName FROM hlstats_Ranks WHERE game='$game' AND '" . (int) $playerdata['kills'] . "' BETWEEN minKills AND maxKills LIMIT 1");
	if ($db->num_rows($rr) > 0) { list($rankRaw) = $db->fetch_row($rr); $rankName = zozo_rank_ru($rankRaw); }
	$rankTier = zozo_rank_tier((int) $playerdata['kills']);
	$db->query("SELECT kill_streak FROM hlstats_Players WHERE playerId = '$player'");
	list($kill_streak) = $db->fetch_row();
	$kills_per_min = ($playerdata['connection_time'] > 0) ? sprintf('%.1f', ($playerdata['kills'] / ($playerdata['connection_time'] / 60))) : '0';

	$avatarTint = function ($seed) {
		$palette = array('#5a845a', '#8e44ad', '#27ae60', '#3498db', '#d63031', '#e8b931', '#14b8a6', '#6366f1');
		$h = 0; $seed = (string) $seed;
		for ($i = 0; $i < strlen($seed); $i++) { $h = (ord($seed[$i]) + ($h << 5) - $h) & 0xffffff; }
		return $palette[$h % count($palette)];
	};
	$initial = mb_strtoupper(mb_substr($playerdata['lastName'], 0, 1, 'UTF-8'), 'UTF-8');
	$nf = function ($n) { return number_format((int) $n, 0, '.', ' '); };
	$flagImg = '';
	if ($g_options['countrydata'] == 1 && !empty($playerdata['flag'])) {
		$alt = ucfirst(strtolower($playerdata['country']));
		$flagImg = '<img src="' . getFlag($playerdata['flag']) . '" class="tableicon" alt="' . htmlspecialchars($alt) . '" title="' . htmlspecialchars($alt) . '" />';
	}

	pageHeader(
		array($gamename, __('chathistory.nav.player_details'), $pl_name),
		array(
			$gamename => $g_options['scripturl'] . "?game=$game",
			__('players.title') => $g_options['scripturl'] . "?mode=players&game=$game",
			__('chathistory.nav.player_details') => ""
		),
		$pl_name
	);
?>
<div class="hero">
	<span class="hero-avatar" style="background:<?php echo $avatarTint($playerdata['lastName']); ?>"><?php echo htmlspecialchars($initial); ?></span>
	<div class="hero-body">
		<h1 class="hero-name"><?php echo $flagImg; ?><?php echo $pl_name; ?></h1>
		<div class="hero-meta">
<?php if ($rankName !== '') { ?><span class="tier-badge"><span class="tier-glyph t<?php echo $rankTier; ?>" aria-hidden="true"></span><?php echo htmlspecialchars($rankName); ?></span><?php } ?>
<?php if ($steamid !== '') { ?><span class="mono hero-id"><?php echo htmlspecialchars($steamid); ?></span><?php } ?>
			<span class="hero-status <?php echo ($hideranking == 2) ? 'bad' : 'ok'; ?>"><?php echo $statusmsg; ?></span>
<?php if (!empty($playerdata['clan_name'])) { ?><span class="hero-clan"><?php echo htmlspecialchars($playerdata['clan_name']); ?></span><?php } ?>
		</div>
	</div>
	<div class="hero-actions">
<?php if ($steamid !== '') { ?><a class="hero-btn" href="https://steamcommunity.com/profiles/<?php echo urlencode($steamid); ?>" target="_blank" rel="noopener"><?=__('playerinfo.hero.steam_profile')?></a><?php } ?>
		<a class="hero-btn ghost" href="<?php echo $g_options['scripturl']; ?>?mode=search&amp;st=player&amp;q=<?php echo $pl_urlname; ?>"><?=__('playerinfo.hero.similar')?></a>
	</div>
</div>

<?php
	// Stat tiles captured once; emitted AFTER the tab bar (mockup order:
	// hero -> tabs -> tiles -> tab content). DEF-13.
	ob_start();
?>
<section class="tiles">
	<div class="tile"><span class="tile-label"><?=__('common.col.points')?></span><span class="tile-val s-red"><?php echo $nf($playerdata['skill']); ?></span></div>
	<div class="tile"><span class="tile-label"><?=__('common.col.kills')?></span><span class="tile-val"><?php echo $nf($playerdata['kills']); ?></span><span class="tile-sub"><?php echo sprintf(__('playerinfo.tile.sub.kpm'), $kills_per_min); ?></span></div>
	<div class="tile"><span class="tile-label"><?=__('common.col.deaths')?></span><span class="tile-val"><?php echo $nf($playerdata['deaths']); ?></span><span class="tile-sub"><?php echo sprintf(__('playerinfo.tile.sub.suicides'), $nf($playerdata['suicides'])); ?></span></div>
	<div class="tile"><span class="tile-label"><?=__('common.col.kpd')?></span><span class="tile-val"><?php echo is_numeric($playerdata['kpd']) ? number_format((float) $playerdata['kpd'], 2) : htmlspecialchars($playerdata['kpd']); ?></span><span class="tile-sub"><?php echo sprintf(__('playerinfo.tile.sub.streak'), $nf($kill_streak)); ?></span></div>
	<div class="tile"><span class="tile-label"><?=__('common.col.headshots')?></span><span class="tile-val"><?php echo $nf($playerdata['headshots']); ?></span><span class="tile-sub"><?php echo htmlspecialchars($playerdata['acc']); ?>% <?=__('common.col.accuracy')?></span></div>
	<div class="tile"><span class="tile-label"><?=__('common.col.connection_time')?></span><span class="tile-val"><?php echo timestamp_to_str($playerdata['connection_time']); ?></span></div>
</section>
<?php
	$tilesHtml = ob_get_clean();
?>

<div class="block" id="main">
<?php
	if ($g_options['playerinfo_tabs'] == '1') {
?>
	<ul class="subsection_tabs" id="tabs_playerinfo">
		<li><a href="#" id="tab_general_aliases"><?=__('claninfo.tab.general')?></a></li>
		<li><a href="#" id="tab_playeractions_teams"><?=__('claninfo.tab.teams_actions')?></a></li>
		<li><a href="#" id="tab_weapons"><?=__('common.nav.weapons')?></a></li>
		<li><a href="#" id="tab_mapperformance_servers"><?=__('playerinfo.tab.maps_servers')?></a></li>
		<li><a href="#" id="tab_killstats"><?=__('playerinfo.tab.killstats')?></a></li>
	</ul><br />
	<?php echo $tilesHtml; ?>
	<div id="main_content"></div>
	<script type="text/javascript">
		var Tabs = new Tabs
		(
			$('main_content'), $$('#main ul.subsection_tabs a'),
			{
				'mode': 'playerinfo',
				'game': '<?php echo $game; ?>',
				'loadingImage': '<?php echo IMAGE_PATH; ?>/ajax.gif',
				'defaultTab': 'general_aliases',
				'extra':
				{
					'player': '<?php echo $player; ?>', 'killLimit': '<?php echo $killLimit; ?>'
				}
			}
		);
	</script>
<?php
	} else {
		echo $tilesHtml;
		echo "\n<div id=\"tabgeneral\" class=\"tab\">\n";
			require_once theme()->pagePath('playerinfo_general') ?? (PAGE_PATH.'/playerinfo_general.php');
			require_once PAGE_PATH.'/playerinfo_aliases.php';
		echo '</div>';
		echo "\n<div id=\"tabteams\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_playeractions.php';
			require_once PAGE_PATH.'/playerinfo_teams.php';
		echo '</div>';
		echo "\n<div id=\"tabweapons\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_weapons.php';
		echo '</div>';
		echo "\n<div id=\"tabmaps\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_mapperformance.php';
			require_once PAGE_PATH.'/playerinfo_servers.php';
		echo '</div>';
		echo "\n<div id=\"tabkills\" class=\"tab\">\n";
			require_once PAGE_PATH.'/playerinfo_killstats.php';
		echo '</div>';
	}
?>
</div>
<div class="block" style="clear:both;padding-top:12px;">
	<div class="subblock">
		<?php // VRTF r3: the "*"-note footnote was removed — its referent (the
		      // starred summary values) is gone from the general tab. ?>
		<div style="float:right;">
			<?php
				if (isset($_SESSION['loggedin'])) {
					echo __('claninfo.admin_options_label') . '<a href="'.$g_options['scripturl']."?mode=admin&amp;task=tools_editdetails_player&amp;id=$player\">" . __('playerinfo.link.edit_player_details') . "</a><br />";
				}
			?>
			<?=__('players.nav.goto_label')?> <a href="<?php echo $g_options['scripturl'] . "?mode=players&amp;game=$game"; ?>"><?=__('players.title')?></a>
		</div>
	</div>
</div>
