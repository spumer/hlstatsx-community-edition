<?php

/**
 * Catalog lint (FEAT-0027-PLAN §6.4): three checks over the i18n key catalogs.
 *
 *   1. key used in code but missing from en.php   -> ERROR (exit 1)
 *   2. key present in ru.php but not used in code -> orphan warning
 *   3. key used in code but missing from ru.php   -> untranslated warning
 *
 * Usage: php scripts/i18n/catalog-lint.php
 */

$root = dirname(__DIR__, 2);
$webDir = $root . '/web';

$en = require $webDir . '/lang/en.php';
$ru = require $webDir . '/lang/ru.php';

$codeKeys = collectKeysFromCode($webDir);

$missingEn = array_diff($codeKeys, array_keys($en));
$orphanedRu = array_diff(array_keys($ru), $codeKeys);
$untranslated = array_diff($codeKeys, array_keys($ru));

if ($missingEn) {
    echo 'ERROR: keys used in code but missing from en.php (' . count($missingEn) . "):\n";
    foreach ($missingEn as $key) {
        echo "  - {$key}\n";
    }
}

if ($orphanedRu) {
    echo 'WARNING: keys present in ru.php but not referenced in code (' . count($orphanedRu) . "):\n";
    foreach ($orphanedRu as $key) {
        echo "  - {$key}\n";
    }
}

if ($untranslated) {
    echo 'WARNING: keys used in code but missing from ru.php, will fall back to en (' . count($untranslated) . "):\n";
    foreach ($untranslated as $key) {
        echo "  - {$key}\n";
    }
}

if (!$missingEn && !$orphanedRu && !$untranslated) {
    echo 'Catalog lint: clean. ' . count($codeKeys) . ' keys in code, ' . count($en) . ' in en.php, ' . count($ru) . " in ru.php.\n";
}

exit($missingEn ? 1 : 0);

/**
 * @return string[] keys referenced via __()/_e()/__f() anywhere under web/,
 *                   excluding the catalog files themselves.
 */
function collectKeysFromCode(string $webDir): array
{
    $keys = [];
    $excluded = [
        realpath($webDir . '/lang/en.php'),
        realpath($webDir . '/lang/ru.php'),
    ];

    foreach (globRecursive($webDir, '*.php') as $file) {
        if (in_array(realpath($file), $excluded, true)) {
            continue;
        }

        $source = file_get_contents($file);

        if (preg_match_all('/\b(?:__|_e|__f)\(\s*([\'"])((?:(?!\1).)*)\1/', $source, $matches)) {
            foreach ($matches[2] as $key) {
                $keys[$key] = true;
            }
        }
    }

    return array_keys($keys);
}

function globRecursive(string $dir, string $pattern): array
{
    $result = glob("{$dir}/{$pattern}") ?: [];

    foreach ((glob("{$dir}/*", GLOB_ONLYDIR) ?: []) as $subdir) {
        $result = array_merge($result, globRecursive($subdir, $pattern));
    }

    return $result;
}
