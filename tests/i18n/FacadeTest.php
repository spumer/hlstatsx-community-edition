<?php

if (!defined('IN_HLSTATS')) {
    define('IN_HLSTATS', true);
}

require_once __DIR__ . '/../../web/includes/i18n.php';

use Service\LanguageService;

$fixtures = __DIR__ . '/../fixtures/lang';

return [
    'i18n facade: default en bind (pre-options-bootstrap) resolves real text, not the key' => function () use ($fixtures) {
        // Mirrors hlstats.php's early bind: only a default 'en' service is
        // attached, before $g_options / the real configured language is
        // known. A page rendered in this window (e.g. error() on a failed
        // DB connect) must still show readable English, not a raw key.
        i18n_bind(new LanguageService($fixtures, 'en'));

        hlx_assert_same('Hello', __('sample.hello'), 'default en bind must resolve real catalog text');
    },

    'i18n facade: __() with nothing bound at all returns the key itself (fail-safe)' => function () {
        $GLOBALS['__i18n_service'] = null;

        hlx_assert_same('some.unbound.key', __('some.unbound.key'), 'unbound facade must return the key untouched');
    },
];
