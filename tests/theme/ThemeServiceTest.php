<?php

use Service\ThemeService;

/*
 * Fixture tree in a throwaway temp dir (mirrors how ThemeService reads a real
 * web root: styles/<skin>.css legacy skins, hlstatsimg/icons/<skin>/ icon dirs,
 * themes/<name>/ packages with a theme.json manifest).
 */
$root = sys_get_temp_dir() . '/hlx-theme-test-' . uniqid();

mkdir($root . '/styles', 0777, true);
mkdir($root . '/hlstatsimg/icons/sourcebans', 0777, true);
mkdir($root . '/themes/zozo/chrome', 0777, true);
mkdir($root . '/themes/zozo/icons', 0777, true);
mkdir($root . '/themes/zozo/pages', 0777, true);
mkdir($root . '/themes/gated/chrome', 0777, true);
mkdir($root . '/themes/gated/pages', 0777, true);

file_put_contents($root . '/styles/sourcebans.css', '/* stock */');
file_put_contents($root . '/styles/classic.css', '/* stock */');
file_put_contents($root . '/themes/zozo/theme.css', '/* zozo */');
file_put_contents($root . '/themes/zozo/hlstats.css', '/* zozo base */');
// zozo overrides chrome by convention (file presence, no manifest whitelist)
file_put_contents($root . '/themes/zozo/chrome/header.php', '<?php /* chrome */');
file_put_contents($root . '/themes/zozo/chrome/footer.php', '<?php /* chrome */');
// zozo overrides page bodies by convention too (file presence, no whitelist)
file_put_contents($root . '/themes/zozo/pages/game.php', '<?php /* page */');
file_put_contents($root . '/themes/zozo/pages/players.php', '<?php /* page */');
file_put_contents($root . '/themes/zozo/theme.json', json_encode([
    'name'     => 'ZoZo',
    'css'      => ['theme.css'],
    'base_css' => 'hlstats.css',
]));

// gated ships both chrome files but its manifest whitelists only header;
// likewise it ships two page files but whitelists only game
file_put_contents($root . '/themes/gated/chrome/header.php', '<?php /* chrome */');
file_put_contents($root . '/themes/gated/chrome/footer.php', '<?php /* chrome */');
file_put_contents($root . '/themes/gated/pages/game.php', '<?php /* page */');
file_put_contents($root . '/themes/gated/pages/players.php', '<?php /* page */');
file_put_contents($root . '/themes/gated/theme.json', json_encode([
    'name'   => 'Gated',
    'chrome' => ['header'],
    'pages'  => ['game'],
]));

register_shutdown_function(function () use ($root) {
    if (!is_dir($root)) {
        return;
    }
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    rmdir($root);
});

$make = function () use ($root) {
    return new ThemeService($root . '/themes', $root . '/styles', $root . '/hlstatsimg', 'sourcebans.css');
};

return [
    'ThemeService: legacy skin resolves to the stock three hrefs' => function () use ($make) {
        $svc = $make();

        hlx_assert_same('sourcebans.css', $svc->resolve('sourcebans.css'), 'legacy name resolves to itself');
        hlx_assert_false($svc->isPackage(), 'legacy skin is not a package');
        hlx_assert_same(
            ['hlstats.css', 'styles/sourcebans.css', 'css/SqueezeBox.css'],
            $svc->styleHrefs(),
            'legacy hrefs must stay byte-parity with header.php:102-104'
        );
    },

    'ThemeService: legacy skin with an icon dir points iconPath at it' => function () use ($make, $root) {
        $svc = $make();
        $svc->resolve('sourcebans.css');

        hlx_assert_same($root . '/hlstatsimg/icons/sourcebans', $svc->iconPath(), 'per-skin icon dir wins');
    },

    'ThemeService: legacy skin without an icon dir falls back to the base icon path' => function () use ($make, $root) {
        $svc = $make();
        $svc->resolve('classic.css');

        hlx_assert_same($root . '/hlstatsimg/icons', $svc->iconPath(), 'no per-skin dir -> base icons path');
    },

    'ThemeService: package theme resolves from its manifest' => function () use ($make) {
        $svc = $make();

        hlx_assert_same('zozo', $svc->resolve('zozo'), 'package name resolves to itself');
        hlx_assert_true($svc->isPackage(), 'themes/zozo/ is a package');
        hlx_assert_same(
            ['themes/zozo/hlstats.css', 'themes/zozo/theme.css', 'css/SqueezeBox.css'],
            $svc->styleHrefs(),
            'package hrefs come from the manifest (base_css override + css list)'
        );
        hlx_assert_same('themes/zozo/icons', $svc->iconPath(), 'package icons live inside the package');
    },

    'ThemeService: empty and null input fall back to the configured default' => function () use ($make) {
        $svc = $make();

        hlx_assert_same('sourcebans.css', $svc->resolve(''), 'empty string -> default');
        hlx_assert_same('sourcebans.css', $svc->resolve(null), 'null -> default');
        hlx_assert_false($svc->isPackage(), 'default is the legacy sourcebans skin');
    },

    'ThemeService: path-traversal names are rejected in favour of the default (fail-safe)' => function () use ($make) {
        $svc = $make();

        hlx_assert_same('sourcebans.css', $svc->resolve('../../etc/passwd'), 'unix traversal rejected');
        hlx_assert_same('sourcebans.css', $svc->resolve('..\\..\\x'), 'windows traversal rejected');
        hlx_assert_same(
            ['hlstats.css', 'styles/sourcebans.css', 'css/SqueezeBox.css'],
            $svc->styleHrefs(),
            'a rejected name must emit the safe default hrefs, never the raw input'
        );
    },

    'ThemeService: an unknown theme name falls back to the default' => function () use ($make) {
        $svc = $make();

        hlx_assert_same('sourcebans.css', $svc->resolve('nope'), 'unknown name -> default (not an error)');
    },

    'ThemeService: package overrides chrome parts whose file exists' => function () use ($make, $root) {
        $svc = $make();
        $svc->resolve('zozo');

        hlx_assert_same($root . '/themes/zozo/chrome/header.php', $svc->chromePath('header'), 'header override resolves to the package file');
        hlx_assert_same($root . '/themes/zozo/chrome/footer.php', $svc->chromePath('footer'), 'footer override resolves to the package file');
    },

    'ThemeService: package does not override chrome parts without a file' => function () use ($make) {
        $svc = $make();
        $svc->resolve('zozo'); // ships no ingame chrome files

        hlx_assert_same(null, $svc->chromePath('ingame_header'), 'no file -> stock ingame header');
        hlx_assert_same(null, $svc->chromePath('ingame_footer'), 'no file -> stock ingame footer');
    },

    'ThemeService: an unknown chrome part is never overridden' => function () use ($make) {
        $svc = $make();
        $svc->resolve('zozo');

        hlx_assert_same(null, $svc->chromePath('sidebar'), 'only the four known parts can be overridden');
    },

    'ThemeService: legacy skin never overrides chrome' => function () use ($make) {
        $svc = $make();
        $svc->resolve('sourcebans.css');

        hlx_assert_same(null, $svc->chromePath('header'), 'legacy skins have no chrome');
        hlx_assert_same(null, $svc->chromePath('footer'), 'legacy skins have no chrome');
    },

    'ThemeService: manifest chrome whitelist narrows overrides' => function () use ($make) {
        $svc = $make();
        $svc->resolve('gated'); // ships header.php AND footer.php, whitelists only header

        hlx_assert_true($svc->chromePath('header') !== null, 'whitelisted part with a file is overridden');
        hlx_assert_same(null, $svc->chromePath('footer'), 'part absent from the whitelist is not overridden even with a file');
    },

    'ThemeService: package overrides page bodies whose file exists' => function () use ($make, $root) {
        $svc = $make();
        $svc->resolve('zozo');

        hlx_assert_same($root . '/themes/zozo/pages/game.php', $svc->pagePath('game'), 'game override resolves to the package file');
        hlx_assert_same($root . '/themes/zozo/pages/players.php', $svc->pagePath('players'), 'players override resolves to the package file');
    },

    'ThemeService: package does not override page bodies without a file' => function () use ($make) {
        $svc = $make();
        $svc->resolve('zozo'); // ships no playerinfo page

        hlx_assert_same(null, $svc->pagePath('playerinfo'), 'no file -> stock page body');
        hlx_assert_same(null, $svc->pagePath('playerinfo_general'), 'no file -> stock page body');
    },

    'ThemeService: legacy skin never overrides page bodies' => function () use ($make) {
        $svc = $make();
        $svc->resolve('sourcebans.css');

        hlx_assert_same(null, $svc->pagePath('game'), 'legacy skins have no page overrides');
        hlx_assert_same(null, $svc->pagePath('players'), 'legacy skins have no page overrides');
    },

    'ThemeService: traversal / malformed page names are never resolved' => function () use ($make) {
        $svc = $make();
        $svc->resolve('zozo');

        hlx_assert_same(null, $svc->pagePath('../../etc/passwd'), 'unix traversal rejected');
        hlx_assert_same(null, $svc->pagePath('..\\..\\game'), 'windows traversal rejected');
        hlx_assert_same(null, $svc->pagePath('game.php'), 'dotted extension name not a whitelisted page key');
        hlx_assert_same(null, $svc->pagePath(''), 'empty name rejected');
    },

    'ThemeService: manifest pages whitelist narrows page overrides' => function () use ($make) {
        $svc = $make();
        $svc->resolve('gated'); // ships game.php AND players.php, whitelists only game

        hlx_assert_true($svc->pagePath('game') !== null, 'whitelisted page with a file is overridden');
        hlx_assert_same(null, $svc->pagePath('players'), 'page absent from the whitelist is not overridden even with a file');
    },

    'ThemeService: listThemes exposes both legacy skins and packages' => function () use ($make) {
        $svc = $make();
        $themes = $svc->listThemes();

        hlx_assert_same('Sourcebans', $themes['sourcebans.css'] ?? null, 'legacy skin label via ucwords');
        hlx_assert_same('Classic', $themes['classic.css'] ?? null, 'second legacy skin present');
        hlx_assert_same('ZoZo', $themes['zozo'] ?? null, 'package label comes from the manifest name');
    },
];
