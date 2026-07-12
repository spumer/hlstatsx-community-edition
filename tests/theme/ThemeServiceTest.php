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

file_put_contents($root . '/styles/sourcebans.css', '/* stock */');
file_put_contents($root . '/styles/classic.css', '/* stock */');
file_put_contents($root . '/themes/zozo/theme.css', '/* zozo */');
file_put_contents($root . '/themes/zozo/hlstats.css', '/* zozo base */');
file_put_contents($root . '/themes/zozo/chrome/header.php', '<?php /* chrome */');
file_put_contents($root . '/themes/zozo/theme.json', json_encode([
    'name'     => 'ZoZo',
    'css'      => ['theme.css'],
    'base_css' => 'hlstats.css',
    'chrome'   => ['header'],
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

    'ThemeService: chromePath is a PR-1 stub and always returns null' => function () use ($make) {
        $svc = $make();
        $svc->resolve('zozo'); // package that ships chrome/header.php

        hlx_assert_same(null, $svc->chromePath('header'), 'chrome override is wired in PR-2, not PR-1');
        hlx_assert_same(null, $svc->chromePath('ingame_header'), 'ingame chrome is PR-2 too');
    },

    'ThemeService: listThemes exposes both legacy skins and packages' => function () use ($make) {
        $svc = $make();
        $themes = $svc->listThemes();

        hlx_assert_same('Sourcebans', $themes['sourcebans.css'] ?? null, 'legacy skin label via ucwords');
        hlx_assert_same('Classic', $themes['classic.css'] ?? null, 'second legacy skin present');
        hlx_assert_same('ZoZo', $themes['zozo'] ?? null, 'package label comes from the manifest name');
    },
];
