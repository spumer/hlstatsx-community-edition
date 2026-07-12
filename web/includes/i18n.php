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
 *
 * A translation whose placeholder count doesn't match $args (e.g. a ru
 * string missing a %s the en original has) must not turn into a fatal
 * render-path error (PHP 8+ throws ArgumentCountError/ValueError out of
 * vsprintf() for this) -- fail-safe invariant, FEAT-0027-PLAN §4.2. Falls
 * back to the unsubstituted resolved string (already ru -> en -> key via
 * __()), logging the mismatch instead of crashing the page.
 */
function __f(string $key, ...$args): string
{
    $format = __($key);

    try {
        return vsprintf($format, $args);
    } catch (\Throwable $e) {
        error_log("i18n: __f('{$key}') placeholder mismatch: " . $e->getMessage());

        return $format;
    }
}

/**
 * SQL-safe variant of __() for catalog values spliced directly into raw
 * SQL string literals (e.g. CONCAT(...) built by playerhistory.php's
 * event-narrative INSERT statements) rather than echoed as HTML. Escapes
 * the resolved translation via the global $db connection's own escaping
 * (mysqli_real_escape_string under the hood, see class_db.php::escape())
 * so a translated value can never break the surrounding SQL syntax.
 */
function __sql(string $key): string
{
    global $db;

    return $db->escape(__($key));
}
