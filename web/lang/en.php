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
    'awards_daily.no_winner'         => 'No Award Winner',
];
