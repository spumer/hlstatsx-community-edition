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
];
