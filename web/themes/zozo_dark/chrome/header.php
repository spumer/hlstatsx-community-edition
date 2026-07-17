<?php
/*
 * ZoZo Dark — app-shell page header (FEAT-0029 phase 2 · v2).
 *
 * Chrome override for the zozo_dark package theme. Rendered in place of
 * pages/header.php via theme()->chromePath('header'). Reproduces the
 * sidebar app-shell of MOCKUP 01 (sidebar nav + topbar) on the ZoZo DS,
 * while preserving the stock header contract: hit/visit counters, style
 * cookie persistence, the <head> stylesheet/script/title emission and the
 * no-JS detection form. The horizontal navbar / breadcrumb / banner of the
 * legacy chrome are replaced by the sidebar + topbar. footer.php closes the
 * tags opened here (main > content > app).
 */

    if (!defined('IN_HLSTATS')) {
        die('Do not access this file directly.');
    }

	// hit counter
	$db->query("UPDATE hlstats_Options SET value=value+1 WHERE keyname='counter_hits';");

	// visit counter
	if (isset($_COOKIE['ELstatsNEO_Visit']) && $_COOKIE['ELstatsNEO_Visit'] == 0) {
		$db->query("UPDATE hlstats_Options SET value=value+1 WHERE keyname='counter_visits';");
		@setcookie('ELstatsNEO_Visit', '1', time() + ($g_options['counter_visit_timeout'] * 60), '/');
	}

	global $game, $mode;

	// Style resolution / whitelist lives in ThemeService (theme() facade).
	$selectedStyle = theme()->name();

	// persist the chosen style for 30 days, as the legacy header did
	if (isset($_POST['stylesheet']) || isset($_COOKIE['style'])) {
		setcookie('style', $selectedStyle, time() + 60 * 60 * 24 * 30);
	}

	$iconpath   = theme()->iconPath();
	$scripturl  = $g_options['scripturl'];
	$gameQuery  = ($game != '') ? '&amp;game=' . $game : '';
	$navMode    = ($mode == '' || $mode == 'contents') ? 'contents' : $mode;

	// Sidebar badge counts (cheap COUNT(*) scoped to the active game).
	// $game is already validated upstream (valid_request), used as stock does.
	$cntPlayers = $cntClans = $cntServers = null;
	if ($game != '') {
		$r = $db->query("SELECT COUNT(*) FROM hlstats_Players WHERE game='$game' AND hideranking = 0");
		list($cntPlayers) = $db->fetch_row($r);
		$r = $db->query("SELECT COUNT(*) FROM hlstats_Clans WHERE game='$game' AND hidden = 0");
		list($cntClans) = $db->fetch_row($r);
		$r = $db->query("SELECT COUNT(*) FROM hlstats_Servers WHERE game='$game'");
		list($cntServers) = $db->fetch_row($r);
	}

	// Active game display name for the sidebar gamebox.
	$gameName = '';
	if ($game != '') {
		$r = $db->query("SELECT name FROM hlstats_Games WHERE code='$game'");
		if ($db->num_rows($r) > 0) {
			list($gameName) = $db->fetch_row($r);
		}
	}

	// nav-link renderer: chip is a formatted count, '—' when unknown.
	$navChip = function ($n) {
		if ($n === null) {
			return '<span class="chip dash">&mdash;</span>';
		}
		return '<span class="chip tnum">' . number_format((int) $n, 0, '.', ' ') . '</span>';
	};
	$navClass = function ($m) use ($navMode) {
		return 'nav-link' . ($navMode === $m ? ' active' : '');
	};
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php foreach (theme()->styleHrefs() as $__href) { ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $__href; ?>" />
<?php } ?>
	<link rel="SHORTCUT ICON" href="favicon.ico" />
	<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/mootools.js"></script>
	<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/SqueezeBox.js"></script>
	<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/heatmap.js"></script>
<?php if ($g_options['playerinfo_tabs'] == '1') { ?>
	<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/tabs.js"></script>
<?php } ?>
	<title>
<?php
	echo $g_options['sitename'];
	foreach ($title as $t) {
		echo " - $t";
	}
?>
	</title>
</head>
<body>
<?php
	// no-JS detection: POST the js flag once; degrade tabs/maps without it
	if (isset($_POST['js']) && $_POST['js']) {
		$_SESSION['nojs'] = 0;
	} else {
		if ((!isset($_SESSION['nojs'])) or ($_SESSION['nojs'] == 1)) {
			echo '
			<form name="jsform" id="jsform" action="" method="post" style="display:none">
			<div>
			<input name="js" type="text" value="true" />
			<script type="text/javascript">
			document.jsform.submit();
			</script>
			</div>
			</form>';
			$_SESSION['nojs'] = 1;
			$g_options['playerinfo_tabs'] = 0;
			$g_options['show_google_map'] = 0;
		}
	}
?>
<div class="app">

	<aside class="sidebar">
		<div class="brand">
			<a href="<?php echo $scripturl; ?>" class="wordmark">Zo<span class="z2">Zo</span></a>
<?php if ($gameName != '') { ?>
			<div class="tagline"><?php echo htmlspecialchars($gameName); ?></div>
<?php } ?>
		</div>
		<nav class="nav" aria-label="<?=__('common.nav.menu')?>">
			<a class="<?php echo $navClass('contents'); ?>" href="<?php echo $scripturl . ($game != '' ? '?game=' . $game : ''); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/></svg>
				<span class="label"><?=__('common.nav.contents')?></span><span class="chip dash">&mdash;</span>
			</a>
			<a class="<?php echo $navClass('players'); ?>" href="<?php echo $scripturl; ?>?mode=players<?php echo $gameQuery; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0"/><path d="M16 8.5a3 3 0 0 1 0 5"/><path d="M18 20a5.2 5.2 0 0 0-3-4.7"/></svg>
				<span class="label"><?=__('common.nav.players')?></span><?php echo $navChip($cntPlayers); ?>
			</a>
			<a class="<?php echo $navClass('clans'); ?>" href="<?php echo $scripturl; ?>?mode=clans<?php echo $gameQuery; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 20v-1a6 6 0 0 1 12 0v1"/><circle cx="10" cy="8" r="3"/><path d="M17 13a4 4 0 0 1 4 4v3"/></svg>
				<span class="label"><?=__('common.nav.clans')?></span><?php echo $navChip($cntClans); ?>
			</a>
			<a class="<?php echo $navClass('servers'); ?>" href="<?php echo $scripturl; ?>?mode=servers<?php echo $gameQuery; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3.5" width="18" height="7" rx="1.5"/><rect x="3" y="13" width="18" height="7" rx="1.5"/><path d="M6.5 7h.01M6.5 16.5h.01"/></svg>
				<span class="label"><?=__('common.nav.servers')?></span><?php echo $navChip($cntServers); ?>
			</a>
			<div class="nav-sec" aria-hidden="true"></div>
			<a class="<?php echo $navClass('weapons'); ?>" href="<?php echo $scripturl; ?>?mode=weapons<?php echo $gameQuery; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 12h4l2-7 4 14 2-7h6"/></svg>
				<span class="label"><?=__('common.nav.weapons')?></span><span class="chip dash">&mdash;</span>
			</a>
			<a class="<?php echo $navClass('maps'); ?>" href="<?php echo $scripturl; ?>?mode=maps<?php echo $gameQuery; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 4-6 3v13l6-3 6 3 6-3V4l-6 3z"/><path d="M9 4v13M15 7v13"/></svg>
				<span class="label"><?=__('common.nav.maps')?></span><span class="chip dash">&mdash;</span>
			</a>
			<a class="<?php echo $navClass('awards'); ?>" href="<?php echo $scripturl; ?>?mode=awards<?php echo $gameQuery; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="9" r="5"/><path d="M9 13.5 7.5 21l4.5-2.5L16.5 21 15 13.5"/></svg>
				<span class="label"><?=__('common.nav.awards')?></span><span class="chip dash">&mdash;</span>
			</a>
			<a class="<?php echo $navClass('actions'); ?>" href="<?php echo $scripturl; ?>?mode=actions<?php echo $gameQuery; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M13 2 4 14h7l-1 8 9-12h-7z"/></svg>
				<span class="label"><?=__('common.nav.actions')?></span><span class="chip dash">&mdash;</span>
			</a>
		</nav>
		<div class="side-spacer"></div>
<?php if ($gameName != '') { ?>
		<div class="gamebox">
			<div class="gmark"><?php echo htmlspecialchars(strtoupper(substr($gameName, 0, 2))); ?></div>
			<div>
				<div class="gname"><?php echo htmlspecialchars($gameName); ?></div>
				<div class="gsub"><?php echo htmlspecialchars($game); ?></div>
			</div>
		</div>
<?php } ?>
	</aside>

	<div class="content-shell">
		<header class="topbar">
			<button class="iconbtn" type="button" aria-label="<?=__('common.nav.menu')?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
			<nav class="breadcrumb" aria-label="<?=__('common.nav.menu')?>">
				<a class="crumb" href="<?php echo $scripturl; ?>">HLstatsX</a>
<?php
			if (is_array($location)) foreach ($location as $l => $url) {
				$url = preg_replace('/%s/', $scripturl, $url);
				$url = preg_replace('/&/', '&amp;', $url);
				echo '<span class="sep"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 6 6 6-6 6"/></svg></span>';
				if ($url) {
					echo '<a class="crumb" href="' . $url . '">' . htmlspecialchars($l) . '</a>';
				} else {
					echo '<span class="current">' . htmlspecialchars($l) . '</span>';
				}
			}
?>
			</nav>
			<form class="search-top" action="<?php echo $scripturl; ?>" method="get" role="search">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></svg>
				<input type="hidden" name="mode" value="search" />
				<input type="text" name="q" placeholder="<?=__('players.search.placeholder')?>" aria-label="<?=__('common.nav.search')?>" />
				<span class="kbd">Ctrl K</span>
			</form>
		</header>

		<main>
<?php
	// Banner (kept from stock: shown on contents, or always when bannerdisplay==1)
	if ($g_options['bannerdisplay'] != 0 && ($mode == 'contents' || $g_options['bannerdisplay'] == 1)) {
?>
		<div class="block" style="text-align:center;">
			<img src="<?php echo ((strncmp($g_options['bannerfile'], 'http:/', 6) == 0) ? $g_options['bannerfile'] : IMAGE_PATH . '/' . $g_options['bannerfile']); ?>" alt="<?=__('header.banner_alt')?>" />
		</div>
<?php } ?>
