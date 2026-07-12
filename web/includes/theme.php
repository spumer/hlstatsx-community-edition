<?php

if (!defined('IN_HLSTATS')) {
    die('Do not access this file directly.');
}

/**
 * Global theme facade over Service\ThemeService.
 *
 * web/pages/*.php (and the standalone status.php / show_graph.php / ingame.php
 * entry points) are @include-d or run outside the DI container, so they have
 * no route to it. theme_bind() is called once per request -- right after the
 * style name is resolved from POST/cookie/option -- to attach the real
 * service; theme() then hands every legacy page the resolver.
 *
 * Mirrors includes/i18n.php's i18n_bind()/__() bridge.
 */

$GLOBALS['__theme_service'] = null;

function theme_bind(\Service\ThemeService $service): void
{
    $GLOBALS['__theme_service'] = $service;
}

function theme(): \Service\ThemeService
{
    $service = $GLOBALS['__theme_service'] ?? null;

    if (!$service instanceof \Service\ThemeService) {
        throw new \RuntimeException('theme() called before theme_bind(); no ThemeService is attached.');
    }

    return $service;
}
