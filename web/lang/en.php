<?php

/**
 * English locale catalog -- the source of truth for en-parity (FEAT-0027-PLAN §6).
 *
 * Every key wrapped in code as __('key') MUST have an entry here, and that
 * entry MUST be byte-for-byte identical to the literal that used to be in
 * the source (see scripts/i18n/token-diff.php). Missing en entries are a
 * build error (scripts/i18n/catalog-lint.php).
 *
 * Grouped by source file; 'common.*' holds strings reused across pages
 * (table columns, navigation, buttons, system messages).
 */

return [
    // common.* -- class_table.php / includes/functions.php (shared across pages)
    'common.col.rank'        => 'Rank',
    'common.msg.error_heading' => 'ERROR',
    'common.msg.empty'       => '---',
    'common.msg.undefined'   => 'Undefined',

    // common.nav.* -- pages/header.php (site chrome, shown on every page)
    'common.nav.contents'    => 'Contents',
    'common.nav.search'      => 'Search',
    'common.nav.help'        => 'Help',
    'common.nav.servers'     => 'Servers',
    'common.nav.chat'        => 'Chat',
    'common.nav.players'     => 'Players',
    'common.nav.clans'       => 'Clans',
    'common.nav.countries'   => 'Countries',
    'common.nav.awards'      => 'Awards',
    'common.nav.actions'     => 'Actions',
    'common.nav.weapons'     => 'Weapons',
    'common.nav.maps'        => 'Maps',
    'common.nav.roles'       => 'Roles',
    'common.nav.bans'        => 'Bans',
    'header.banner_alt'      => 'Banner',

    // common.col.* -- reusable table-column labels (first used by pages/players.php)
    'common.col.player'          => 'Player',
    'common.col.points'           => 'Points',
    'common.col.activity'         => 'Activity',
    'common.col.connection_time'  => 'Connection Time',
    'common.col.kills'             => 'Kills',
    'common.col.deaths'            => 'Deaths',
    'common.col.kpd'               => 'K:D',
    'common.col.headshots'         => 'Headshots',
    'common.col.hpk'                => 'HS:K',
    'common.col.accuracy'           => 'Accuracy',

    // pages/players.php
    'players.title'              => 'Player Rankings',
    'players.search.label'       => 'Find a player:',
    'players.search.submit'      => 'Search',
    'players.rankview.label'     => 'Ranking View',
    'players.rankview.submit'    => 'View',
    'players.rankview.total'     => 'Total Ranking',
    'players.rankview.lastweek'  => 'Last Week',
    'players.rankview.lastmonth' => 'Last Month',
    'players.col.mmrank'         => 'Rank',
    'players.minkills.pre'       => 'Only show players with',
    'players.minkills.post'      => 'or more kills.',
    'players.minkills.submit'    => 'Apply',
    'players.nav.goto_label'     => 'Go to:',
    'players.nav.clan_rankings'  => 'Clan Rankings',

    // common.col.* -- more reusable table-column labels (first used by search.php/search-class.php)
    'common.col.id'    => 'ID',
    'common.col.game'  => 'Game',
    'common.col.tag'   => 'Tag',
    'common.col.name'  => 'Name',

    // pages/search.php + pages/search-class.php
    'search.type.player'          => 'Player Names',
    'search.type.ip'               => 'Player IP Addresses',
    'search.type.clan'             => 'Clan Names',
    'search.form.title'            => 'Find a Player or Clan',
    'search.form.query_label'      => 'Search For:',
    'search.form.type_label'       => 'In:',
    'search.form.game_label'       => 'Game:',
    'search.form.game_all'         => '(All)',
    'search.form.submit'           => 'Find Now',
    'search.results.title'         => 'Search Results',
    'search.results.count_prefix'  => 'Search results:',
    'search.results.count_suffix'  => 'items matching',

    // pages/search-class.php: Search::$uniqueid_string(_plural), moved into
    // the constructor since __() can't be a property default (§4.4/PR review).
    'search.uniqueid.singular'     => 'Unique ID',
    'search.uniqueid.plural'       => 'Unique IDs',
    'search.uniqueid.ip_singular'  => 'IP Address',
    'search.uniqueid.ip_plural'    => 'IP Addresses',

    // pages/awards.php
    'awards.title'       => 'Awards Info',
    'awards.tab.daily'   => 'Daily&nbsp;Awards',
    'awards.tab.global'  => 'Global&nbsp;Awards',
    'awards.tab.ranks'   => 'Ranks',
    'awards.tab.ribbons' => 'Ribbons',

    // pages/awards_daily.php
    // The " Awards ($awards_d_date)" suffix is one interpolated string
    // (real $-interpolation, not just a plain literal) -- "Awards" inside
    // it needs a structural split to extract safely; deferred, see batch
    // report (same category as the search-class.php property-default
    // refactor: token-diff can't fold across live interpolation).
    'awards_daily.period_daily'     => 'Daily',
    'awards_daily.period_day_suffix' => 'Day',
    'awards.no_winner'         => 'No Award Winner',

    // pages/awards_global.php
    'awards_global.title' => 'Global Awards',

    // pages/awards_ranks.php (title reuses awards.tab.ranks)
    // '&nbsp;kills)' word "kills" NOT extracted: immediately chained via
    // "." to an unrelated adjacent literal ('<br />'), and the token-diff
    // fold groups by raw dot-adjacency with no logical-boundary awareness,
    // so wrapping it would merge that unrelated literal into the same
    // reconstructed token and break byte-parity. Flagged, see batch report.
    'awards_ranks.player_list_fallback' => 'Player List',
    'awards_ranks.achieved_prefix'      => 'Achieved by ',
    'awards_ranks.achieved_suffix'      => ' Players',

    // pages/awards_ribbons.php (title reuses awards.tab.ribbons; prefix
    // reuses awards_ranks.achieved_prefix, byte-identical 'Achieved by ')
    // "Ribbon Class #$i1 ($cnt awards required)" header NOT extracted:
    // fully $-interpolated string (two variables mid-string), so
    // splitting it into concatenation pieces is a structural change, not
    // a plain wrap -- flagged, see batch report (candidate for __f() with
    // two placeholders once that's sanctioned as a deliberate step).
    'awards_ribbons.achieved_suffix' => ' players',

    // pages/dailyawardinfo.php
    'dailyawardinfo.no_award_id'         => 'No award ID specified.',
    'dailyawardinfo.title'                => 'Award Details',
    'dailyawardinfo.breadcrumb_awards'    => 'Awards Statistics',
    'dailyawardinfo.breadcrumb_details'   => 'Awards Details',
    'dailyawardinfo.col.day'              => 'Day',
    'dailyawardinfo.col.count'            => 'Count for the Day',
    'dailyawardinfo.section_title'        => 'Daily Award Details',
    'dailyawardinfo.back_to'              => 'Back to ',
    'dailyawardinfo.back_to_link'         => 'Daily Awards',

    // pages/rankinfo.php (breadcrumb + "Back to" reuse
    // awards.tab.ranks/dailyawardinfo.back_to; TableColumn reuses
    // common.col.player/common.col.kills)
    'rankinfo.no_rank_id'    => 'No rank ID specified.',
    'rankinfo.invalid_game'  => 'Invalid or no game specified.',
    'rankinfo.title'         => 'Rank Details',
    'rankinfo.section_title' => 'Rank Details',
    'rankinfo.col.skill'     => 'Skill',
    'rankinfo.back_to_link'  => 'Ranks',

    // pages/ribboninfo.php (breadcrumb + "Back to" reuse
    // awards.tab.ribbons/dailyawardinfo.back_to; TableColumn reuses
    // common.col.player). error("No such game '$game'.") NOT extracted:
    // $-interpolated, same deferred category as elsewhere -- see report.
    'ribboninfo.no_ribbon_id'      => 'No ribbon ID specified.',
    'ribboninfo.title'             => 'Ribbon Details',
    'ribboninfo.section_title'     => 'Ribbon Details',
    'ribboninfo.col.daily_awards'  => 'Daily awards',
    'ribboninfo.times_suffix'      => ' times',
    'ribboninfo.back_to_link'      => 'Ribbons',

    // pages/clans.php (title/section_title/footer-link reuse
    // players.nav.clan_rankings/players.title/players.nav.goto_label;
    // TableColumn reuses common.col.tag/activity/connection_time/kills/
    // deaths/kpd; submit buttons reuse players.search.submit/
    // players.minkills.submit). error("No such game '$game'.") NOT
    // extracted -- same deferred $-interpolation category.
    'clans.col.clan'             => 'Clan',
    'clans.col.avg_points'       => 'Avg. Points',
    'clans.col.members'          => 'Members',
    'clans.search.label'         => 'Find a clan:',
    'clans.minmembers.pre'       => 'Show only clans with',
    'clans.minmembers.post'      => 'or more members from a total of ',
    'clans.minmembers.total_suffix' => ' clans',

    // pages/countryclans.php (title/section_title/footer-link reuse
    // countryclans.title/players.nav.goto_label; TableColumn reuses
    // clans.col.avg_points/members + common.col.activity/
    // connection_time/kills/deaths/kpd; submit reuses
    // players.minkills.submit). error("No such game '$game'.") NOT
    // extracted -- same deferred category. minmembers.pre kept separate
    // from clans.minmembers.pre despite byte-identical English (the
    // fork copy-pasted "Show only clans with" here verbatim, even
    // though this page is about countries) because our RU source uses
    // a different, context-correct translation for each page.
    'countryclans.title'              => 'Country Rankings',
    'countryclans.col.country'        => 'Country',
    'countryclans.minmembers.pre'          => 'Show only clans with',
    'countryclans.minmembers.post'         => 'or more members from a total of ',
    // NOTE: 'countrys' is a typo in the original fork source, preserved
    // verbatim for en-parity -- not a mistake introduced here.
    'countryclans.minmembers.total_suffix' => ' countrys',

    // pages/countryclansinfo.php (breadcrumb reuses countryclans.title;
    // TableColumn reuses players.col.mmrank/common.col.points/activity/
    // kills/deaths). error("No such countryclan '$flag'.") NOT extracted
    // -- $-interpolated, same deferred category as elsewhere.
    'countryclansinfo.no_country_id'          => 'No country ID specified.',
    'countryclansinfo.title'                  => 'Country Details',
    'countryclansinfo.section_title'          => 'Country Information',
    'countryclansinfo.stats_summary'          => 'Statistics Summary',
    'countryclansinfo.row.country'            => 'Country:',
    'countryclansinfo.row.activity'           => 'Activity:',
    'countryclansinfo.row.members'            => 'Members:',
    'countryclansinfo.active_members'         => 'active members',
    'countryclansinfo.row.total_kills'        => 'Total Kills:',
    'countryclansinfo.row.total_deaths'       => 'Total Deaths:',
    'countryclansinfo.row.avg_kills'          => 'Avg. Kills:',
    'countryclansinfo.row.kills_per_death'    => 'Kills per Death:',
    'countryclansinfo.row.kills_per_minute'   => 'Kills per Minute:',
    'countryclansinfo.row.avg_member_points'  => 'Avg. Member Points:',
    'countryclansinfo.row.avg_connection_time' => 'Avg. Connection Time:',
    'countryclansinfo.row.total_connection_time' => 'Total Connection Time:',
    'countryclansinfo.col.name'               => 'Name',
    'countryclansinfo.col.time'                => 'Time',
    'countryclansinfo.col.clan_kills'          => 'Clan Kills',
    'countryclansinfo.col.kpd'                  => 'Kpd',
    'countryclansinfo.members_title'          => 'Members',

    // pages/contents.php (breadcrumb/title reuse common.nav.contents;
    // table header/icon-alt reuse common.nav.players/clans where the
    // fork's English matches). The big interpolated summary paragraph
    // ("<strong>N</strong> players and <strong>M</strong> clans ranked
    // in...") is NOT extracted -- three concatenated pieces, each with
    // multiple live $-interpolations, would need a full sentence
    // restructure into __f() with five placeholders; that's a deliberate
    // future step, not a plain wrap. Flagged, see batch report.
    'contents.games_title'            => 'Games',
    'contents.col.game'                => 'Game',
    'contents.col.top_player'          => 'Top Player',
    'contents.col.top_clan'            => 'Top Clan',
    'contents.clan_icon_alt'           => 'Clan Rankings',
    'contents.player_icon_alt'         => 'Player Rankings',
    'contents.general_stats_title'    => 'General Statistics',
    'contents.last_kill_label'        => 'Last Kill',
    'contents.stats_disclaimer.pre'   => 'All statistics are generated in real-time. Event history data expires after ',
    'contents.stats_disclaimer.post'  => ' days.',

    // pages/roles.php (title reuses itself for both pageHeader and
    // printSectionTitle; TableColumn reuses common.col.kills/deaths/kpd;
    // footer nav reuses players.nav.goto_label). error("No such game
    // '$game'.") NOT extracted -- same deferred $-interpolation category.
    'roles.title'         => 'Role Statistics',
    'roles.col.role'      => 'Role',
    'roles.col.picked'    => 'Picked',
    'roles.col.ratio'     => 'Ratio',
    'roles.stats.pre'     => 'From a total of ',
    'roles.stats.mid'     => ' kills with ',
    'roles.stats.post'    => ' deaths',

    // pages/rolesinfo.php (invalid_game reuses rankinfo.invalid_game;
    // TableColumn Player reuses common.col.player; back_to reuses
    // dailyawardinfo.back_to; section_title reuses breadcrumb_details).
    // Note the fork's own inconsistency, preserved as-is: 'Roles Details'
    // (title) vs 'Role Details' (breadcrumb/section_title) vs
    // 'Roles Statistics' (breadcrumb) vs roles.php's 'Role Statistics'.
    'rolesinfo.no_role_id'         => 'No role ID specified.',
    'rolesinfo.title'              => 'Roles Details',
    'rolesinfo.breadcrumb_roles'   => 'Roles Statistics',
    'rolesinfo.breadcrumb_details' => 'Role Details',
    'rolesinfo.section_title'      => 'Role Details',
    'rolesinfo.col.kills_suffix'   => ' kills',
    'rolesinfo.stats.pre'          => 'From a total of ',
    'rolesinfo.stats.mid'          => ' kills as ',
    'rolesinfo.headshots_with'     => 'with',
    'rolesinfo.headshots_word'     => 'headshots',
    'rolesinfo.stats.days_pre'     => '(Last ',
    'rolesinfo.stats.days_post'    => ' Days)',
    'rolesinfo.back_to_link'       => 'Roles Statistics',

    // pages/maps.php (title reused for pageHeader value+key and
    // printSectionTitle; TableColumn reuses common.col.kills/headshots/
    // hpk + roles.col.ratio; stats.pre reuses roles.stats.pre byte-
    // identical 'From a total of '; footer nav reuses
    // players.nav.goto_label). error("No such game '$game'.") NOT
    // extracted -- same deferred category as elsewhere.
    'maps.title'        => 'Map Statistics',
    'maps.col.map'      => 'Map',
    'maps.col.heatmap'  => 'HeatMap',
    'maps.stats.mid'    => ' kills with ',
    'maps.stats.post'   => ' headshots',

    // pages/claninfo.php (breadcrumb/title reuse players.nav.
    // clan_rankings/players.title-family keys where applicable; tab
    // labels reuse common.nav.weapons/maps; footer nav reuses
    // players.nav.goto_label/clan_rankings). error("No such clan
    // '$clan'.") and the "Edit Clan Details" link text (inside a
    // $clan-interpolated string) NOT extracted -- same deferred
    // $-interpolation category. admin_options_label is a
    // concatenation-fold verified isolated from the interpolated
    // remainder by an intervening $g_options[...] array-access token.
    'claninfo.no_clan_id'         => 'No clan ID specified.',
    'claninfo.title'              => 'Clan Details',
    'claninfo.tab.general'        => 'General',
    'claninfo.tab.teams_actions'  => 'Teams &amp; Actions',
    'claninfo.marked_note.pre'    => 'Items marked "*" above are generated from the last ',
    'claninfo.marked_note.post'   => ' days.',
    'claninfo.admin_options_label' => 'Admin Options: ',

    // pages/claninfo_general.php (Statistics Summary table reuses
    // countryclansinfo.row.*/col.* -- same row set as countryclansinfo.php,
    // this page adds Home Page/Favorite Server/Map/Weapon rows on top).
    // " active members ($totalclanplayers total)" NOT extracted -- $-
    // interpolated, __f() candidate, same deferred category as elsewhere.
    // 'Unknown' ($fav_weapon fallback) NOT extracted -- internal image-
    // lookup sentinel, not user-facing text. '-' (empty-stat placeholder)
    // NOT extracted -- bare literal, same as countryclansinfo.php precedent.
    'claninfo_general.section_title'          => 'Clan Information',
    'claninfo_general.label.clan'              => 'Clan:',
    'claninfo_general.label.homepage'          => 'Home Page:',
    'claninfo_general.homepage_not_specified'  => '(Not specified.)',
    'claninfo_general.label.favorite_server'   => 'Favorite Server:*',
    'claninfo_general.label.favorite_map'      => 'Favorite Map:*',
    'claninfo_general.label.favorite_weapon'   => 'Favorite Weapon:*',
    'claninfo_general.col.player_locations'    => 'Player Locations',

    // pages/claninfo_actions.php ('Action'/'Points Bonus' each reused
    // across both the Player Actions and Victims tables on this page)
    'claninfo_actions.col.action'             => 'Action',
    'claninfo_actions.col.achieved'           => 'Achieved',
    'claninfo_actions.col.points_bonus'       => 'Points Bonus',
    'claninfo_actions.title.player_actions'   => 'Player Actions *',
    'claninfo_actions.col.times_victimized'   => 'Times Victimized',
    'claninfo_actions.title.victims'          => 'Victims of Player-Player Actions *',

    // pages/claninfo_teams.php ('Joined' reused across the teamcount
    // and rolecount columns; Role/Ratio/Kills/Deaths/K:D columns reuse
    // roles.col.role/ratio + common.col.kills/deaths/kpd)
    'claninfo_teams.col.team'                 => 'Team',
    'claninfo_teams.col.joined'               => 'Joined',
    'claninfo_teams.col.percentage_of_times'  => 'Percentage of Times',
    'claninfo_teams.title.team_selection'     => 'Team Selection *',
    'claninfo_teams.title.role_selection'     => 'Role Selection *',

    // pages/claninfo_weapons.php ('Weapon'/'Hits' reused across all 3
    // tables on this page; Kills/Headshots/Accuracy reuse common.col.*).
    // The 12-column "else" branch (used only when
    // $g_options['show_weapon_target_flash'] == 0) and the Adobe Flash
    // fallback message ("The hitbox display requires...") are NOT
    // extracted -- zozo's own copy of this file left both untranslated
    // (byte-identical to upstream English), so there is no RU source to
    // draw from; flagged for a team decision (fresh-translate now under
    // plan sect 5.4, or leave English as legacy/dead Flash-era code).
    'claninfo_weapons.col.weapon'                  => 'Weapon',
    'claninfo_weapons.col.points_modifier'         => 'Points Modifier',
    'claninfo_weapons.col.percentage_of_kills'     => 'Percentage of Kills',
    'claninfo_weapons.col.percentage_of_headshots' => 'Percentage of Headshots',
    'claninfo_weapons.col.hpk'                      => 'Hpk',
    'claninfo_weapons.title.weapon_usage'          => 'Weapon Usage *',
    'claninfo_weapons.col.shots'                    => 'Shots',
    'claninfo_weapons.col.hits'                     => 'Hits',
    'claninfo_weapons.col.damage'                   => 'Damage',
    'claninfo_weapons.col.kills_per_death'         => 'Kills per Death',
    'claninfo_weapons.col.damage_per_hit'          => 'Damage per Hit',
    'claninfo_weapons.col.shots_per_kill'          => 'Shots per Kill',
    'claninfo_weapons.title.weapon_stats'          => 'Weapon Stats *',
    'claninfo_weapons.col.left'                     => 'Left',
    'claninfo_weapons.col.middle'                   => 'Middle',
    'claninfo_weapons.col.right'                    => 'Right',
    'claninfo_weapons.title.weapon_targets'        => 'Weapon Targets *',
    'claninfo_weapons.col.targets_header'          => 'Targets',
    'claninfo_weapons.link.show_total_stats'       => 'Show total target statistics',

    // pages/claninfo_mapperformance.php (Kills/Deaths/Headshots reuse
    // common.col.*; the four Percentage of Kills/Headshots, Kills per
    // Death, and Hpk columns reuse claninfo_weapons.col.* -- same
    // byte-identical labels as claninfo_weapons.php)
    'claninfo_mapperformance.col.map_name' => 'Map Name',
    'claninfo_mapperformance.title'        => 'Map Performance *',

    // pages/chat.php (breadcrumb/title RU value follows zozo's own
    // simplified wording for this position, common.nav.chat/common.col.
    // player/maps.col.map reused for TableColumn labels; footer nav
    // reuses players.nav.goto_label). error("No such game '$game'.")
    // NOT extracted -- same deferred $-interpolation category as
    // elsewhere. sprintf('%s %s Server Chat Log (Last %d Days)', ...)
    // NOT extracted -- __f() candidate (3 placeholders), deferred per
    // the batch-2 instruction to decide __f() adoption in one pass at
    // the end. "Clear" button and the delay-notice message are fork-
    // only additions with no zozo RU source; translated fresh here as
    // unambiguous UI vocabulary (not flagged, unlike the __f() items).
    'chat.title'                  => 'Server Chat Statistics',
    'chat.default.all_servers'    => '(All Servers)',
    'chat.default.unknown_server' => '(Unknown Server)',
    'chat.col.date'                => 'Date',
    'chat.col.message'             => 'Message',
    'chat.col.server'              => 'Server',
    'chat.label.show_chat_from'   => 'Show Chat from',
    'chat.option.all_servers'     => 'All Servers',
    'chat.label.filter'            => 'Filter:',
    'chat.btn.view'                 => 'View',
    'chat.btn.clear'                => 'Clear',
    'chat.msg.delay_pre'           => '*Messages are delayed by ',
    'chat.msg.delay_post'          => ' minutes to prevent real-time tracking.',

    // pages/chathistory.php (players.title/chat.col.date/message/server,
    // maps.col.map, chat.label.filter/btn.view/btn.clear, and
    // players.nav.goto_label all reused byte-identical). "No player ID
    // specified or invalid ID." is fresh-translated (no zozo RU source --
    // zozo left this exact message in English on every page that has it).
    // error("No such player '$player'.") and
    // sprintf('Player Chat History (Last %d Days)', $deleteDaysSafe) NOT
    // extracted -- same deferred $-interpolation/__f() categories as
    // elsewhere.
    'chathistory.no_player_id'          => 'No player ID specified or invalid ID.',
    'chathistory.title'                  => 'Chat History',
    'chathistory.nav.player_details'    => 'Player Details',
    'chathistory.suffix.statistics'     => '\'s Statistics',

    // pages/actions.php (Action column reuses claninfo_actions.col.action
    // byte-for-byte; footer nav reuses players.nav.goto_label). error(
    // "No such game '$game'.") NOT extracted -- same deferred category.
    'actions.title'      => 'Action Statistics',
    'actions.col.earned' => 'Earned',
    'actions.col.reward' => 'Reward',
    'actions.stats.pre'  => 'From a total of ',
    'actions.stats.post' => ' earned actions',

    // pages/actioninfo.php (breadcrumb reuses actions.title;
    // common.col.player/claninfo_actions.col.achieved/times_victimized
    // reused byte-for-byte). "No action ID specified."/"Invalid or no
    // game specified." are fresh-translated -- zozo left both in
    // English on this page. actioninfo.back_to.action_stats is a
    // separate key from actions.title despite identical EN text: the RU
    // wording needs the dative case ("to the Actions") here, same
    // pattern as rankinfo.back_to_link vs awards.tab.ranks.
    'actioninfo.no_action_id'          => 'No action ID specified.',
    'actioninfo.invalid_game'          => 'Invalid or no game specified.',
    'actioninfo.title'                  => 'Action Details',
    'actioninfo.col.skill_bonus_total' => 'Skill Bonus Total',
    'actioninfo.stats.mid1'            => ' from a total of ',
    'actioninfo.stats.mid2'            => ' achievements (Last ',
    'actioninfo.stats.post'            => ' Days)',
    'actioninfo.back_to.label'         => 'Back to ',
    'actioninfo.back_to.action_stats'  => 'Action Statistics',
    'actioninfo.title.victims'         => 'Action Victim Details',
    'actioninfo.victims.label'         => 'Victims of ',
    'actioninfo.victims.days_pre'      => ' (Last ',

    // pages/weapons.php (Weapon column reuses claninfo_weapons.col.weapon;
    // Ratio/Headshots/HS:K reuse roles.col.ratio/common.col.headshots/hpk
    // byte-for-byte; stats line reuses roles.stats.pre + maps.stats.mid/
    // post; footer nav reuses players.nav.goto_label). error(
    // "No such game '$game'.") NOT extracted -- same deferred category.
    // weapons.col.kills is a separate key from common.col.kills despite
    // identical EN text ('Kills') -- zozo used a different RU word here
    // ('Убито' vs 'Убийств'), a stylistic choice preserved as-authored
    // rather than silently unified.
    'weapons.title'        => 'Weapon Statistics',
    'weapons.col.modifier' => 'Modifier',
    'weapons.col.kills'    => 'Kills',

    // pages/weaponinfo.php (breadcrumb value reuses weapons.title;
    // common.col.player/headshots and claninfo_weapons.col.hpk reused
    // byte-for-byte; error("Invalid or no game specified.") reuses
    // actioninfo.invalid_game byte-for-byte; stats line reuses
    // roles.stats.pre + maps.stats.mid; "Back to " reuses
    // actioninfo.back_to.label). "No weapon ID specified." is fresh-
    // translated -- zozo left it in English on this page too.
    // weaponinfo.col.kills_suffix and weaponinfo.back_to.weapon_stats
    // are separate keys from their same-EN-text counterparts
    // (rolesinfo.col.kills_suffix, weapons.title) since RU differs
    // (different verb; dative case).
    'weaponinfo.no_weapon_id'        => 'No weapon ID specified.',
    'weaponinfo.title'                => 'Weapon Details',
    'weaponinfo.col.kills_suffix'    => ' kills',
    'weaponinfo.stats.mid2'          => ' headshots (Last ',
    'weaponinfo.back_to.weapon_stats' => 'Weapon Statistics',

    // pages/mapinfo.php (breadcrumb reuses maps.title; common.col.player/
    // headshots and claninfo_weapons.col.hpk reused byte-for-byte;
    // actioninfo.invalid_game/back_to.label/stats.post and roles.stats.pre
    // reused byte-for-byte). "No map specified." fresh-translated -- zozo
    // left it in English on this page too. mapinfo.back_to.map_stats is a
    // separate key from maps.title despite identical EN text since RU
    // needs the dative case here.
    //
    // NOT extracted -- deferred, $-interpolated (would require
    // restructuring a single interpolated string into a concatenation,
    // not just substituting an existing dot-joined literal -- same
    // deferred category as elsewhere, unlike the claninfo.php
    // concatenation-folds which substituted into pre-existing dot chains):
    // - "Kills on $map" (TableColumn label)
    // - "<p><a href=\"$map_dlurl\">Download this map...</a></p>"
    // - "Heatmap: $map" (image title attribute) -- also same ambiguous
    //   "heatmap" term as the documented maps.col.heatmap gap
    'mapinfo.no_map'              => 'No map specified.',
    'mapinfo.title'                => 'Map Details',
    'mapinfo.stats.mid'            => ' kills (Last ',
    'mapinfo.back_to.map_stats'   => 'Map Statistics',

    // pages/servers.php (maps.col.map/weapons.col.kills/
    // claninfo_weapons.col.hpk reused byte-for-byte; period labels
    // reused for both the visible <td> text and the graph <img alt>
    // attribute, unifying zozo's own inconsistency -- zozo translated
    // the <td> text but left the alt attributes in English).
    // servers.col.headshots is a separate key from common.col.headshots
    // despite identical EN text: RU needs the nominative plural here
    // ("Хедшоты") vs the genitive used elsewhere ("Хедшотов").
    // "Invalid server ID provided." and the two printSectionTitle
    // strings are fresh-translated -- zozo left all three in English.
    // error("No such game '$game'.") NOT extracted -- same deferred
    // category as elsewhere. The "(Join)" text inside the $addr-
    // interpolated steam:// link is NOT extracted -- would require
    // introducing new concatenation not present in the baseline, same
    // reasoning as the reverted mapinfo.php "Download this map..." wrap.
    'servers.invalid_server_id' => 'Invalid server ID provided.',
    'servers.title.live_view'    => 'Server Live View',
    'servers.title.load_history' => 'Server Load History',
    'servers.col.server'          => 'Server',
    'servers.col.address'         => 'Address',
    'servers.col.played'          => 'Played',
    'servers.col.players'         => 'Players',
    'servers.col.headshots'       => 'Headshots',
    'servers.period.24h'          => '24h View',
    'servers.period.last_week'    => 'Last Week',
    'servers.period.last_month'   => 'Last Month',
    'servers.period.last_year'    => 'Last Year',

    // pages/livestats.php (common.col.player/hpk and
    // countryclansinfo.col.time reused byte-for-byte). livestats.col.kills
    // is a separate key from common.col.kills/weapons.col.kills: this
    // header spans a combined kills/deaths/hpk column cluster, and zozo
    // translated it as the K:D abbreviation rather than literally
    // "Kills". "Unknown team" (empty-team-name fallback) has no zozo
    // equivalent at all -- zozo's file lacks this ternary entirely -- so
    // it's fresh-translated, same as "Unknown" (connection-time fallback,
    // 2 occurrences reusing one key) and " wins)" (safe to fold: the
    // baseline already dot-concatenates '&nbsp;(' . $map_teama_wins .
    // ' wins)', so replacing the existing trailing literal keeps the same
    // 2-dot chain rather than introducing new structure).
    'livestats.col.kills'      => 'Kills',
    'livestats.col.hs'          => 'Hs',
    'livestats.col.acc'         => 'Acc',
    'livestats.col.lat'         => 'Lat',
    'livestats.col.skill'       => 'Skill',
    'livestats.unknown_team'   => 'Unknown team',
    'livestats.msg.unknown'    => 'Unknown',
    'livestats.suffix.wins'    => ' wins)',
    'livestats.msg.no_players' => 'No Players',

    // pages/playerinfo_servers.php (Server/Kills/Ratio/Deaths/K:D/
    // Headshots/HS:K all reuse existing keys byte-for-byte).
    // playerinfo_servers.col.percentage_of_headshots is a separate key
    // from claninfo_weapons.col.percentage_of_headshots despite identical
    // EN text -- zozo translated it differently on this page ("Percent
    // of Headshots" vs "Ratio"), preserved as originally authored.
    'playerinfo_servers.col.percentage_of_headshots' => 'Percentage of Headshots',
    'playerinfo_servers.title' => 'Server Activity *',

    // pages/playerinfo_aliases.php (countryclansinfo.col.time and
    // common.col.kpd/headshots/hpk/accuracy reused byte-for-byte).
    // playerinfo_aliases.col.name is a separate key from common.col.name
    // (different word entirely -- "nickname" in this player-alias
    // context vs the generic "Название" used elsewhere). kills/deaths
    // are separate keys from common.col.kills/deaths too: zozo used the
    // nominative plural here ("Убийства"/"Смерти") instead of the
    // genitive used elsewhere ("Убийств"/"Смертей").
    'playerinfo_aliases.col.name'      => 'Name',
    'playerinfo_aliases.col.last_use'  => 'Last Use',
    'playerinfo_aliases.col.kills'     => 'Kills',
    'playerinfo_aliases.col.deaths'    => 'Deaths',
    'playerinfo_aliases.col.suicides'  => 'Suicides',
    'playerinfo_aliases.title'          => 'Aliases',

    // pages/playerinfo_playeractions.php (claninfo_actions.col.action and
    // actions.col.earned reused byte-for-byte; the victims table's
    // section title reuses claninfo_actions.title.victims exactly, since
    // that phrase applies identically to a clan's or a single player's
    // list of victim-actions). playerinfo_playeractions.title is a
    // separate key from claninfo_actions.title.player_actions despite
    // identical EN text: RU needs the singular ("игрока") on this
    // single-player page vs the plural ("игроков") used for a clan's
    // collective actions.
    'playerinfo_playeractions.col.accumulated_points' => 'Accumulated Points',
    'playerinfo_playeractions.title'                  => 'Player Actions *',
    'playerinfo_playeractions.col.earned_against'     => 'Earned Against',
];
