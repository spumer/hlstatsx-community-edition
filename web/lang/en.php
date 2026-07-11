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
];
