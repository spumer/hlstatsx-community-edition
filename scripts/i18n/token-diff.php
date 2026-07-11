<?php

/**
 * Static en-parity guard for a single i18n-wrapped page (FEAT-0027-PLAN §6.2).
 *
 * Proves two things about ONE file at once:
 *   (a) the only delta versus the pre-i18n revision is literal -> wrap, i.e.
 *       nothing else in the file was touched;
 *   (b) every wrap's web/lang/en.php entry is byte-for-byte identical to the
 *       literal that used to sit there.
 *
 * How: every recognized wrap in the current file is textually reconstructed
 * back to plain pre-i18n source (using its en.php value), and the result is
 * compared byte-for-byte against the pre-i18n snapshot. Any remaining diff
 * means either an unrelated edit slipped in, or a wrap's en value is wrong.
 *
 * Recognizes exactly what this pipeline's extraction emits:
 *   1. <?=__('key')?>                    HTML text-node / attribute embed
 *   2. __('key')                         bare PHP-argument literal replacement
 *   3. 'literal' . __('key') . 'literal' concatenation fold (e.g. a label
 *      spliced out of the middle of a bigger echo'd string), any chain
 *      length, folded back into a single literal using the quote style of
 *      the first literal neighbour (default single-quote if the whole
 *      chain is bare calls).
 * (_e()/__f() are supported by the runtime facade -- see web/includes/i18n.php
 * -- but this pilot's extraction only emits the shapes above, so only those
 * are modeled here.)
 *
 * Usage:
 *   git show master:web/pages/players.php > /tmp/before_players.php
 *   php scripts/i18n/token-diff.php web/pages/players.php --before=/tmp/before_players.php
 */

$root = dirname(__DIR__, 2);
$args = parseArgs($argv);

if (!isset($args['path'], $args['before'])) {
    fwrite(STDERR, "Usage: php scripts/i18n/token-diff.php <path-relative-to-repo> --before=<pre-i18n-snapshot-file>\n");
    exit(2);
}

$currentPath = $root . '/' . $args['path'];

if (!is_file($currentPath)) {
    fwrite(STDERR, "File not found: {$currentPath}\n");
    exit(2);
}

if (!is_file($args['before'])) {
    fwrite(STDERR, "Baseline snapshot not found: {$args['before']}\n");
    exit(2);
}

// This repo checks out CRLF (core.autocrlf=true) but git blobs (what
// `git show ref:path` returns) are LF-normalized. Normalize both sides so
// the comparison is about content, not EOL style.
$current = str_replace("\r\n", "\n", file_get_contents($currentPath));
$before = str_replace("\r\n", "\n", file_get_contents($args['before']));
$en = require $root . '/web/lang/en.php';

[$reconstructed, $wraps, $errors] = reconstructAsEn($current, $en);

foreach ($errors as $error) {
    echo "ERROR: {$error}\n";
}

if ($errors) {
    exit(1);
}

if ($reconstructed === $before) {
    echo 'OK    ' . $args['path'] . ': ' . count($wraps) . " wrap(s), reconstructed en text matches baseline byte-for-byte.\n";
    foreach ($wraps as $key) {
        echo "      - {$key}\n";
    }
    exit(0);
}

echo 'FAIL  ' . $args['path'] . ": reconstructed en text does NOT match baseline.\n";
echo diffPreview($before, $reconstructed);
exit(1);

// -- helpers --------------------------------------------------------------

function parseArgs(array $argv): array
{
    $out = [];

    foreach (array_slice($argv, 1) as $arg) {
        if (str_starts_with($arg, '--before=')) {
            $out['before'] = substr($arg, strlen('--before='));
        } elseif (!isset($out['path'])) {
            $out['path'] = $arg;
        }
    }

    return $out;
}

/**
 * @return array{0: string, 1: string[], 2: string[]} [reconstructed text, wrapped keys, errors]
 */
function reconstructAsEn(string $source, array $en): array
{
    $wraps = [];
    $errors = [];

    // Shape 1: <?=__('key')?>  (must run before Shape 2/3, which would
    // otherwise also match the __('key') sitting inside the short-echo tag).
    $source = preg_replace_callback(
        '/<\?=\s*__\(\s*([\'"])((?:(?!\1).)*)\1\s*\)\s*\?>/',
        function ($m) use ($en, &$wraps, &$errors) {
            $key = $m[2];
            $wraps[] = $key;

            if (!array_key_exists($key, $en)) {
                $errors[] = "key '{$key}' used in <?=__()?> has no entry in en.php";
                return $m[0];
            }

            return $en[$key];
        },
        $source
    );

    if ($errors) {
        return [$source, $wraps, $errors];
    }

    // Shape 2 + 3: find every remaining literal or __('key') call, group
    // consecutive ones joined only by "." into chains, and fold any chain
    // containing a call back into a single literal.
    $tokenPattern = '/\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"|__\(\s*(?:\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*")\s*\)/';

    preg_match_all($tokenPattern, $source, $m, PREG_OFFSET_CAPTURE);
    $tokens = $m[0];

    $chains = [];
    $current = [];

    foreach ($tokens as $token) {
        [$text, $offset] = $token;

        if ($current === []) {
            $current[] = $token;
            continue;
        }

        $prev = $current[count($current) - 1];
        $prevEnd = $prev[1] + strlen($prev[0]);
        $between = substr($source, $prevEnd, $offset - $prevEnd);

        if (preg_match('/^\s*\.\s*$/', $between)) {
            $current[] = $token;
        } else {
            $chains[] = $current;
            $current = [$token];
        }
    }

    if ($current !== []) {
        $chains[] = $current;
    }

    // Splice back-to-front so earlier offsets stay valid as we edit $source.
    for ($c = count($chains) - 1; $c >= 0; $c--) {
        $chain = $chains[$c];
        $hasCall = false;

        foreach ($chain as [$text, ]) {
            if (str_starts_with($text, '__(')) {
                $hasCall = true;
                break;
            }
        }

        if (!$hasCall) {
            continue;
        }

        $value = '';
        $quoteChar = null;
        $chainKeys = [];
        $chainError = null;

        foreach ($chain as [$text, ]) {
            if (str_starts_with($text, '__(')) {
                preg_match('/__\(\s*([\'"])((?:(?!\1).)*)\1\s*\)/', $text, $mm);
                $key = $mm[2];
                $chainKeys[] = $key;

                if (!array_key_exists($key, $en)) {
                    $chainError = "key '{$key}' has no entry in en.php";
                    break;
                }

                $value .= $en[$key];
            } else {
                $quoteChar ??= $text[0];
                $value .= decodePhpStringLiteral($text);
            }
        }

        $wraps = array_merge($wraps, $chainKeys);

        if ($chainError !== null) {
            $errors[] = $chainError;
            continue;
        }

        $start = $chain[0][1];
        $last = $chain[count($chain) - 1];
        $end = $last[1] + strlen($last[0]);

        $replacement = encodePhpStringLiteral($value, $quoteChar ?? "'");
        $source = substr_replace($source, $replacement, $start, $end - $start);
    }

    return [$source, $wraps, $errors];
}

function decodePhpStringLiteral(string $tokenText): string
{
    // This is OUR own already-committed-or-working-tree source, not
    // attacker input, so evaluating it as a literal expression is a safe
    // and exact way to get PHP's own view of the value.
    return eval("return {$tokenText};");
}

function encodePhpStringLiteral(string $value, string $quoteChar): string
{
    if ($quoteChar === '"') {
        $escaped = strtr($value, [
            '\\' => '\\\\',
            '"'  => '\\"',
            '$'  => '\\$',
            "\n" => '\\n',
            "\t" => '\\t',
            "\r" => '\\r',
        ]);

        return '"' . $escaped . '"';
    }

    $escaped = strtr($value, [
        '\\' => '\\\\',
        "'"  => "\\'",
    ]);

    return "'" . $escaped . "'";
}

function diffPreview(string $expected, string $actual): string
{
    $expectedLines = explode("\n", $expected);
    $actualLines = explode("\n", $actual);
    $max = max(count($expectedLines), count($actualLines));
    $out = '';

    for ($i = 0; $i < $max; $i++) {
        $e = $expectedLines[$i] ?? '<EOF>';
        $a = $actualLines[$i] ?? '<EOF>';

        if ($e !== $a) {
            $out .= '  line ' . ($i + 1) . ":\n";
            $out .= "    baseline: {$e}\n";
            $out .= "    current:  {$a}\n";
        }
    }

    return $out ?: "  (no line-level diff found -- check trailing whitespace/EOL)\n";
}
