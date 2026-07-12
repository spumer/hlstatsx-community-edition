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
 * @return string[] keys referenced via __()/_e()/__f()/__sql() anywhere
 *                   under web/, excluding the catalog files themselves.
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

        foreach (extractKeysFromFile($file) as $key) {
            $keys[$key] = true;
        }
    }

    return array_keys($keys);
}

/**
 * Uses token_get_all(), not regex, for the same reason as
 * scripts/i18n/token-diff.php: a regex scanning for quote characters
 * textually will misread an apostrophe inside a `//` comment (e.g.
 * "doesn't") as a string delimiter. token_get_all() classifies comments
 * (T_COMMENT/T_DOC_COMMENT) correctly, so that can't happen here.
 *
 * @return string[]
 */
function extractKeysFromFile(string $file): array
{
    $tokens = token_get_all(file_get_contents($file));
    $tokens = array_values(array_filter($tokens, function ($token) {
        return !is_array($token) || !in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true);
    }));

    $keys = [];
    $n = count($tokens);

    for ($i = 0; $i < $n; $i++) {
        $token = $tokens[$i];

        if (!is_array($token) || $token[0] !== T_STRING || !in_array($token[1], ['__', '_e', '__f', '__sql'], true)) {
            continue;
        }

        if (!isset($tokens[$i + 1]) || $tokens[$i + 1] !== '(') {
            continue;
        }

        if (!isset($tokens[$i + 2]) || !is_array($tokens[$i + 2]) || $tokens[$i + 2][0] !== T_CONSTANT_ENCAPSED_STRING) {
            continue;
        }

        $keys[] = decodePhpStringLiteral($tokens[$i + 2][1]);
    }

    return $keys;
}

function decodePhpStringLiteral(string $tokenText): string
{
    // This is OUR own source, not attacker input, so evaluating it as a
    // literal expression is a safe and exact way to get PHP's own view
    // of the value (handles escapes the same way PHP itself would).
    return eval("return {$tokenText};");
}

function globRecursive(string $dir, string $pattern): array
{
    $result = glob("{$dir}/{$pattern}") ?: [];

    foreach ((glob("{$dir}/*", GLOB_ONLYDIR) ?: []) as $subdir) {
        $result = array_merge($result, globRecursive($subdir, $pattern));
    }

    return $result;
}
