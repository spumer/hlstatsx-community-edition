<?php
/**
 * Minimal self-contained test runner (no PHPUnit/Composer dependency).
 *
 * Usage: php tests/run.php
 *
 * Each *Test.php file under tests/ returns an array of
 * ['test name' => callable] pairs. A test passes if the callable
 * returns without throwing.
 */

$root = dirname(__DIR__);
require $root . '/web/includes/autoload.php';

function hlx_assert_same($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            $message . ' -- expected ' . var_export($expected, true) . ', got ' . var_export($actual, true)
        );
    }
}

function hlx_assert_true($condition, string $message): void
{
    if ($condition !== true) {
        throw new RuntimeException($message . ' -- expected true');
    }
}

function hlx_assert_false($condition, string $message): void
{
    if ($condition !== false) {
        throw new RuntimeException($message . ' -- expected false');
    }
}

$testFiles = glob($root . '/tests/*/*Test.php');
sort($testFiles);

$total = 0;
$failures = 0;

foreach ($testFiles as $file) {
    $cases = require $file;

    if (!is_array($cases)) {
        continue;
    }

    foreach ($cases as $name => $case) {
        $total++;

        try {
            $case();
            echo "PASS  {$name}\n";
        } catch (Throwable $e) {
            $failures++;
            echo "FAIL  {$name}\n";
            echo '      ' . $e->getMessage() . "\n";
        }
    }
}

echo "\n{$total} tests, " . ($total - $failures) . " passed, {$failures} failed.\n";

exit($failures > 0 ? 1 : 0);
