<?php

if (!defined('IN_HLSTATS')) {
    die('Do not access this file directly.');
}

/**
 * Global i18n facade over Service\LanguageService.
 *
 * web/pages/*.php are @include-d into hlstats.php's scope rather than
 * constructed objects, so they have no route to a DI container. These
 * three functions are the bridge: i18n_bind() is called once (hlstats.php,
 * right after $g_options is resolved) to attach the real service; __()/_e()/
 * __f() are then available to every legacy page and to functions.php itself.
 */

$GLOBALS['__i18n_service'] = null;

function i18n_bind(\Service\LanguageService $service): void
{
    $GLOBALS['__i18n_service'] = $service;
}

/**
 * Resolve a symbolic key to its translated string.
 * Fallback chain: active locale -> en -> the key itself (fail-safe, never throws).
 */
function __(string $key): string
{
    $service = $GLOBALS['__i18n_service'] ?? null;

    if (!$service instanceof \Service\LanguageService) {
        return $key;
    }

    return $service->get($key);
}

function _e(string $key): void
{
    echo __($key);
}

/**
 * Only for keys whose translation already contains sprintf-style
 * placeholders (%s, %d, ...). Do not use to glue unrelated fragments
 * together -- see the fragment-wrapping rule in FEAT-0027-PLAN §5.4.
 */
function __f(string $key, ...$args): string
{
    return vsprintf(__($key), $args);
}
