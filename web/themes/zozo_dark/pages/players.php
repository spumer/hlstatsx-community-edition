<?php
/*
 * ZoZo Dark — players (leaderboard) body, redesign of MOCKUP 02.
 *
 * Theme page-body override for mode=players, resolved via
 * theme()->pagePath('players'). Reuses the stock Table object purely
 * for sort/pagination STATE and the stock ranking SQL verbatim, but
 * emits its own DS leaderboard (horizontal zebra by <tr>, avatar
 * column, tier glyph + rankName from hlstats_Ranks) instead of the
 * legacy Table::draw() vertical bg1/bg2 render. Sortable headers reuse
 * the stock getSortArrow(); pagination is a compact DS re-emit of the
 * same makeQueryString() links. The tier glyph is the DS placeholder
 * (sage mask) pending the founder's tier icon set (task #23).
 */

	if (!defined('IN_HLSTATS')) {
		die('Do not access this file directly.');
	}

	require_once __DIR__ . '/_rank_ru.php';

	// Player Rankings
	$db->query("SELECT hlstats_Games.name FROM hlstats_Games WHERE hlstats_Games.code = '$game'");
	if ($db->num_rows() < 1) {
		error(__f('common.err.no_such_game', $game));
	}
	list($gamename) = $db->fetch_row();
	$db->free_result();

	if (isset($_GET['minkills'])) {
		$minkills = valid_request($_GET['minkills'], true);
	} else {
		$minkills = 1;
	}

	pageHeader(
		array($gamename, __('players.title')),
		array($gamename => "%s?game=$game", __('players.title') => '')
	);

	$rank_type = filter_input(INPUT_GET, 'rank_type', FILTER_VALIDATE_INT, [
		'options' => ['default' => 0, 'min_range' => -2, 'max_range' => 50]
	]);

	$db->query("
		SELECT hlstats_Players_History.eventTime
		FROM hlstats_Players_History
		GROUP BY hlstats_Players_History.eventTime
		ORDER BY hlstats_Players_History.eventTime DESC
		LIMIT 0, 50
	");
	$i = 1;
	$dates = array();
	$options = [
		0  => __('players.rankview.total'),
		-1 => __('players.rankview.lastweek'),
		-2 => __('players.rankview.lastmonth')
	];
	while ($rowdata = $db->fetch_array()) {
		$dates[] = $rowdata;
		$options[$i++] = $rowdata['eventTime'];
	}
	$maxRank = count($dates);
	if ($rank_type === -1 || $rank_type === -2) {
		$rank_type = $rank_type;
	} elseif ($maxRank > 0) {
		$rank_type = max(0, min($rank_type, $maxRank));
	} else {
		$rank_type = 0;
	}

	// Stock Table columns (kept identical so sort keys + defaults match the SQL).
	if ($g_options['rankingtype'] != 'kills') {
		$cols = array(
			new TableColumn('lastName', __('common.col.player'), 'width=26&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k')),
			new TableColumn('mmrank', __('players.col.mmrank'), 'width=4&type=elorank'),
			new TableColumn('skill', __('common.col.points'), 'width=7&align=right&skill_change=1'),
			new TableColumn('activity', __('common.col.activity'), 'width=10&sort=no&type=bargraph'),
			new TableColumn('connection_time', __('common.col.connection_time'), 'width=10&align=right&type=timestamp'),
			new TableColumn('kills', __('common.col.kills'), 'width=7&align=right'),
			new TableColumn('deaths', __('common.col.deaths'), 'width=7&align=right'),
			new TableColumn('kpd', __('common.col.kpd'), 'width=6&align=right'),
			new TableColumn('headshots', __('common.col.headshots'), 'width=6&align=right'),
			new TableColumn('hpk', __('common.col.hpk'), 'width=6&align=right'),
			new TableColumn('acc', __('common.col.accuracy'), 'width=6&align=right&append=' . urlencode('%')),
		);
	} else {
		$cols = array(
			new TableColumn('lastName', __('common.col.player'), 'width=30&flag=1&link=' . urlencode('mode=playerinfo&amp;player=%k')),
			new TableColumn('activity', __('common.col.activity'), 'width=10&sort=no&type=bargraph'),
			new TableColumn('kills', __('common.col.kills'), 'width=7&align=right'),
			new TableColumn('deaths', __('common.col.deaths'), 'width=7&align=right'),
			new TableColumn('kpd', __('common.col.kpd'), 'width=6&align=right'),
			new TableColumn('headshots', __('common.col.headshots'), 'width=6&align=right'),
			new TableColumn('hpk', __('common.col.hpk'), 'width=6&align=right'),
			new TableColumn('acc', __('common.col.accuracy'), 'width=6&align=right&append=' . urlencode('%')),
			new TableColumn('skill', __('common.col.points'), 'width=7&align=right&skill_change=1'),
			new TableColumn('connection_time', __('common.col.connection_time'), 'width=10&align=right&type=timestamp'),
		);
	}
	$table = new Table($cols, 'playerId', $g_options['rankingtype'], 'kpd', true);

	// Stock ranking SQL (verbatim), branched on rank_type.
	if ($rank_type == "0") {
		$result = $db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				hlstats_Players.playerId,
				hlstats_Players.connection_time,
				unhex(replace(hex(hlstats_Players.lastName), 'E280AE', '')) as lastName,
				hlstats_Players.flag, hlstats_Players.country,
				hlstats_Players.skill, hlstats_Players.mmrank,
				hlstats_Players.kills, hlstats_Players.deaths,
				hlstats_Players.last_skill_change,
				ROUND(hlstats_Players.kills/(IF(hlstats_Players.deaths=0, 1, hlstats_Players.deaths)), 2) AS kpd,
				hlstats_Players.headshots,
				ROUND(hlstats_Players.headshots/(IF(hlstats_Players.kills=0, 1, hlstats_Players.kills)), 2) AS hpk,
				IFNULL(ROUND((hlstats_Players.hits / hlstats_Players.shots * 100), 1), 0) AS acc,
				activity
			FROM hlstats_Players
			WHERE hlstats_Players.game = '$game'
				AND hlstats_Players.hideranking = 0
				AND hlstats_Players.kills >= $minkills
			ORDER BY $table->sort $table->sortorder, $table->sort2 $table->sortorder, hlstats_Players.lastName ASC
			LIMIT $table->startitem, $table->numperpage
		");
		$resultCount = $db->query("SELECT FOUND_ROWS()");
		list($numitems) = $db->fetch_row($resultCount);
	} else {
		if ($rank_type == "-1") {
			$maxEvent = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			$minEvent = $maxEvent - (86400 * 7);
		} else if ($rank_type == "-2") {
			$maxEvent = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			$minEvent = $maxEvent - (86400 * 30);
		}
		if (!isset($minEvent)) {
			$minEvent = explode("-", $dates[$rank_type - 1]['eventTime']);
			$minEvent = mktime(0, 0, 0, $minEvent[1], $minEvent[2], $minEvent[0]);
			$maxEvent = $minEvent + 86400;
		}
		$result = $db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				hlstats_Players_History.playerId,
				hlstats_Players.lastName, hlstats_Players.flag, hlstats_Players.country, hlstats_Players.mmrank,
				SUM(hlstats_Players_History.connection_time) AS connection_time,
				SUM(hlstats_Players_History.skill_change) AS skill,
				SUM(hlstats_Players_History.skill_change) AS skill_change,
				SUM(hlstats_Players_History.skill_change) AS last_skill_change,
				SUM(hlstats_Players_History.kills) AS kills,
				SUM(hlstats_Players_History.deaths) AS deaths,
				ROUND(SUM(hlstats_Players_History.kills) / IF(SUM(hlstats_Players_History.deaths) = 0, 1, SUM(hlstats_Players_History.deaths)), 2) AS kpd,
				SUM(hlstats_Players_History.headshots) AS headshots,
				ROUND(SUM(hlstats_Players_History.headshots) / SUM(hlstats_Players_History.kills), 2) AS hpk,
				IFNULL(ROUND((SUM(hlstats_Players_History.hits) / SUM(hlstats_Players_History.shots) * 100), 1), 0) AS acc,
				activity
			FROM hlstats_Players_History
			INNER JOIN hlstats_Players ON hlstats_Players_History.playerId = hlstats_Players.playerId
			WHERE hlstats_Players_History.game = '$game'
				AND hlstats_Players.hideranking = 0
				AND activity > 0
				AND UNIX_TIMESTAMP(hlstats_Players_History.eventTime) >= $minEvent
				AND UNIX_TIMESTAMP(hlstats_Players_History.eventTime) <= $maxEvent
			GROUP BY hlstats_Players_History.playerId
			HAVING SUM(hlstats_Players_History.kills) >= $minkills
			ORDER BY $table->sort $table->sortorder, $table->sort2 $table->sortorder, hlstats_Players.lastName ASC
			LIMIT $table->startitem, $table->numperpage
		");
		$resultCount = $db->query("SELECT FOUND_ROWS()");
		list($numitems) = $db->fetch_row($resultCount);
	}

	// Rank bands (kills -> rankName) loaded once; mapped in PHP so the SQL is untouched.
	$ranks = array();
	$rr = $db->query("SELECT rankName, minKills, maxKills FROM hlstats_Ranks WHERE game='$game' ORDER BY minKills ASC");
	while ($rrow = $db->fetch_array($rr)) { $ranks[] = $rrow; }
	$rankOf = function ($kills) use ($ranks) {
		foreach ($ranks as $r) {
			if ($kills >= $r['minKills'] && ($r['maxKills'] == 0 || $kills <= $r['maxKills'])) {
				return $r['rankName'];
			}
		}
		return '';
	};

	$avatarTint = function ($seed) {
		$palette = array('#5a845a', '#8e44ad', '#27ae60', '#3498db', '#d63031', '#e8b931', '#14b8a6', '#6366f1');
		$h = 0; $seed = (string) $seed;
		for ($i = 0; $i < strlen($seed); $i++) { $h = (ord($seed[$i]) + ($h << 5) - $h) & 0xffffff; }
		return $palette[$h % count($palette)];
	};

	// Sortable header helper: reuse the stock arrow-link, wrapped in our <th>.
	$th = function ($name, $title, $sortable = true, $right = false) use ($table) {
		$cls = $right ? ' class="r"' : '';
		echo "<th$cls>";
		if ($sortable) {
			echo getSortArrow($table->sort, $table->sortorder, $name, $title, $table->var_sort, $table->var_sortorder, $table->sorthash);
		} else {
			echo htmlspecialchars($title);
		}
		echo "</th>";
	};
	$numpages = ceil($numitems / $table->numperpage);
	$rank = ($table->page - 1) * $table->numperpage + 1;
?>
<div class="page-head">
	<h1><?=__('players.title')?></h1>
	<p class="sub"><?php echo htmlspecialchars($gamename); ?> <span class="dot">·</span> <?php echo number_format((int)$numitems, 0, '.', ' '); ?> <?=__('common.nav.players')?></p>
</div>

<!-- Filter toolbar (MOCKUP 02: search + Period + Sort + apply/clear + chips;
     the "Server" filter is dropped per founder decision — stock ranking is
     game-scoped, so that control would be dead). -->
<?php
	// Sortable columns for the Sort dropdown (value = SQL sort key).
	$sortCols = array(
		'skill'           => __('common.col.points'),
		'kills'           => __('common.col.kills'),
		'deaths'          => __('common.col.deaths'),
		'kpd'             => __('common.col.kpd'),
		'acc'             => __('common.col.accuracy'),
		'headshots'       => __('common.col.headshots'),
		'connection_time' => __('common.col.connection_time'),
	);
?>
<div class="toolbar">
	<form class="tb-search" method="get" action="<?php echo $g_options['scripturl']; ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></svg>
		<input type="hidden" name="mode" value="search" />
		<input type="hidden" name="game" value="<?php echo $game; ?>" />
		<input type="hidden" name="st" value="player" />
		<input type="text" name="q" maxlength="64" placeholder="<?=__('players.search.placeholder')?>" />
	</form>
	<form class="tb-filter" method="get" action="<?php echo $g_options['scripturl']; ?>">
		<input type="hidden" name="mode" value="players" />
		<input type="hidden" name="game" value="<?php echo $game; ?>" />
		<div class="tb-group">
			<label class="tb-label"><?=__('players.rankview.label')?></label>
			<select name="rank_type">
				<?php foreach ($options as $value => $label): ?>
					<option value="<?=$value?>" <?=($rank_type == $value) ? 'selected' : ''?>><?=htmlspecialchars($label)?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="tb-group">
			<label class="tb-label"><?=__('players.filter.sort')?></label>
			<select name="sort">
				<?php foreach ($sortCols as $col => $label): ?>
					<option value="<?=$col?>" <?=($table->sort == $col) ? 'selected' : ''?>><?=htmlspecialchars($label)?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<button class="tb-btn" type="submit"><?=__('players.filter.apply')?></button>
	</form>
<?php
	// Active-filter chips (always show current view + sort as removable chips;
	// "Очистить фильтры" only when something differs from defaults).
	$hasFilters = ($rank_type != 0) || ($table->sort != $g_options['rankingtype']);
?>
	<div class="tb-chips">
		<span class="chip flt"><?=__('players.rankview.label')?>: <?php echo htmlspecialchars($options[$rank_type] ?? ''); ?></span>
<?php if (isset($sortCols[$table->sort])) { ?>
		<span class="chip flt"><?=__('players.filter.sort')?>: <?php echo htmlspecialchars($sortCols[$table->sort]); ?></span>
<?php } ?>
<?php if ($hasFilters) { ?>
		<a class="tb-clear" href="<?php echo $g_options['scripturl']; ?>?mode=players&amp;game=<?php echo $game; ?>"><?=__('players.filter.clear')?></a>
<?php } ?>
	</div>
</div>

<section class="panel">
	<table class="lb">
		<thead>
			<tr>
				<th class="r"><?=__('common.col.rank')?></th>
				<th></th>
				<?php $th('lastName', __('common.col.player')); ?>
				<th><?=__('common.col.rank_title')?></th>
				<?php $th('skill', __('common.col.points'), true, true); ?>
				<?php $th('kills', __('common.col.kills'), true, true); ?>
				<?php $th('deaths', __('common.col.deaths'), true, true); ?>
				<?php $th('kpd', __('common.col.kpd'), true, true); ?>
				<?php $th('acc', __('common.col.accuracy'), true, true); ?>
				<?php $th('headshots', __('common.col.headshots'), true, true); ?>
				<?php $th('connection_time', __('common.col.connection_time'), true, true); ?>
			</tr>
		</thead>
		<tbody>
<?php
	while ($row = $db->fetch_array($result)) {
		$nm = $row['lastName'];
		$initial = mb_strtoupper(mb_substr($nm, 0, 1, 'UTF-8'), 'UTF-8');
		$rankNameRaw = $rankOf((int) $row['kills']);
		$rankName = zozo_rank_ru($rankNameRaw);
		$rankTier = zozo_rank_tier((int) $row['kills']);
		$flagImg = '';
		if ($g_options['countrydata'] == 1 && !empty($row['flag'])) {
			$alt = ucfirst(strtolower($row['country']));
			$flagImg = '<img src="' . getFlag($row['flag']) . '" class="tableicon" alt="' . htmlspecialchars($alt) . '" title="' . htmlspecialchars($alt) . '" />';
		}
?>
			<tr>
				<td class="idx r"><?php echo $rank; ?></td>
				<td><span class="avatar sm" style="background:<?php echo $avatarTint($nm); ?>"><?php echo htmlspecialchars($initial); ?></span></td>
				<td class="pl"><a href="<?php echo $g_options['scripturl']; ?>?mode=playerinfo&amp;player=<?php echo $row['playerId']; ?>"><?php echo $flagImg; ?><span class="nm"><?php echo htmlspecialchars($nm); ?></span></a></td>
				<td class="tier"><?php if ($rankName !== '') { ?><span class="tier-glyph tier--<?php echo $rankTier; ?>" aria-hidden="true"></span><span class="tier-name"><?php echo htmlspecialchars($rankName); ?></span><?php } else { ?><span class="num dim">?</span><?php } ?></td>
				<td class="num r pts"><?php echo number_format((int) $row['skill'], 0, '.', ' '); ?></td>
				<td class="num r"><?php echo number_format((int) $row['kills'], 0, '.', ' '); ?></td>
				<td class="num r"><?php echo number_format((int) $row['deaths'], 0, '.', ' '); ?></td>
				<td class="num r"><?php echo htmlspecialchars($row['kpd']); ?></td>
				<td class="num r"><?php echo htmlspecialchars($row['acc']); ?>%</td>
				<td class="num r"><?php echo number_format((int) $row['headshots'], 0, '.', ' '); ?></td>
				<td class="num r dim"><?php echo timestamp_to_str($row['connection_time']); ?></td>
			</tr>
<?php $rank++; } ?>
		</tbody>
	</table>
</section>

<?php if ($numpages > 1) {
	$start = max(1, $table->page - 10);
	$end = min($numpages, $start + 19);
	$start = max(1, $end - 19);
	$pageUrl = function ($n) use ($g_options) { return eHtml($g_options['scripturl'] . '?' . makeQueryString('page', $n)); };
?>
<nav class="pager">
	<?php if ($table->page > 1) { ?><a class="pg" href="<?php echo $pageUrl($table->page - 1); ?>">&laquo;</a><?php } ?>
	<?php for ($p = $start; $p <= $end; $p++) {
		if ($p == $table->page) { echo '<span class="pg cur">' . $p . '</span>'; }
		else { echo '<a class="pg" href="' . $pageUrl($p) . '">' . $p . '</a>'; }
	} ?>
	<?php if ($table->page < $numpages) { ?><a class="pg" href="<?php echo $pageUrl($table->page + 1); ?>">&raquo;</a><?php } ?>
</nav>
<?php } ?>
