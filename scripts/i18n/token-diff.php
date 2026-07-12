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
 * Shape 2/3 detection uses PHP's own token_get_all(), not regex, precisely
 * because regex-matching quote characters textually is wrong: a lone
 * apostrophe inside a `//` comment (e.g. "doesn't") or inside an
 * interpolated string's non-literal part reads as a string delimiter to a
 * naive scanner and corrupts quote-pairing for the rest of the file.
 * token_get_all() classifies comments and interpolated-string pieces as
 * their own token types, so this can't happen.
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

/**
 * Exact text blocks that an authorized infra commit is allowed to ADD on
 * top of the pre-i18n baseline for a given file -- never a replacement of
 * existing content, only ever an insertion. This gate still fails on any
 * other delta; this list exists so that a genuinely-authorized wiring
 * change (e.g. the require_once that pulls in web/includes/i18n.php)
 * doesn't get flagged as "something else changed" alongside real wraps.
 *
 * web/includes/functions.php is require()'d directly by six other entry
 * points besides hlstats.php (autocomplete.php, status.php, sig.php,
 * show_graph.php, ingame.php, trend_graph.php), none of which route
 * through hlstats.php's i18n_bind() wiring -- so functions.php must stay
 * self-sufficient (require the facade itself) rather than relying solely
 * on hlstats.php, or __() would be an undefined-function fatal on every
 * one of those entry points. Keep this list to the smallest number of
 * blocks that keeps that guarantee true; each entry should cite, in the
 * commit that adds it, which entry points require it.
 *
 * web/pages/footer.php's three <?php echo "\n"; ?> statements exist for a
 * different reason: PHP unconditionally swallows exactly one newline
 * immediately following ANY closing "?>" (including "<?=...?>"). Each of
 * these three wraps replaced raw HTML text that had a real trailing
 * newline before the next tag (<br>, <a>); once wrapped, that newline
 * sat right after the wrap's own "?>" and got silently eaten at runtime,
 * even though this tool's source-level reconstruction still matched
 * baseline byte-for-byte (it doesn't simulate PHP's swallow behavior).
 * The echo statement supplies the missing newline explicitly, and its own
 * closing "?>" swallows the template's original (unchanged) newline
 * instead -- net effect: render output is byte-identical to baseline
 * again, without altering the wrap, the catalog value, or any existing
 * template whitespace.
 */
const AUTHORIZED_INSERTIONS = [
    'web/includes/functions.php' => [
        "require_once __DIR__ . '/i18n.php';\n\n",
    ],
    'web/pages/footer.php' => [
        '<?php echo "\n"; ?>',
        '<?php echo "\n"; ?>',
        '<?php echo "\n"; ?>',
    ],
];

/**
 * Quote style for a BARE __('key') call (no adjacent literal to inherit
 * a style from) defaults to single-quote, matching how virtually every
 * literal this pipeline has wrapped so far. The rare original that was
 * double-quoted for no escaping reason (just author preference) needs
 * an explicit override here, or the gate false-fails on an otherwise
 * correct wrap -- e.g. includes/functions.php's `return "Undefined";`.
 *
 * Keyed by [file][key] rather than just [key]: a shared/reused key can
 * legitimately have been double-quoted in one file's original literal
 * and single-quoted in another's (e.g. clans.col.members -- singly
 * quoted in clans.php, but countryclans.php's TableColumn call happens
 * to double-quote all of its arguments), so the override has to be
 * scoped per file, not global to the key.
 */
const BARE_CALL_QUOTE_OVERRIDES = [
    'web/includes/functions.php' => [
        'common.msg.undefined' => '"',
    ],
    'web/pages/countryclans.php' => [
        'clans.col.members' => '"',
    ],
    'web/pages/claninfo.php' => [
        'claninfo.no_clan_id' => '"',
    ],
    'web/pages/chat.php' => [
        'chat.default.all_servers'    => '"',
        'chat.default.unknown_server' => '"',
    ],
    'web/pages/actioninfo.php' => [
        'actioninfo.title.victims' => '"',
    ],
    'web/pages/servers.php' => [
        'servers.invalid_server_id' => '"',
    ],
    'web/pages/livestats.php' => [
        'livestats.unknown_team'    => '"',
        'livestats.msg.no_players'  => '"',
    ],
    'web/pages/playerawards.php' => [
        'playerawards.no_player_id'    => '"',
        'playerawards.no_award_id_bug' => '"',
    ],
    'web/pages/playersessions.php' => [
        'playerawards.no_player_id' => '"',
    ],
    'web/pages/playerinfo.php' => [
        'playerawards.no_player_id' => '"',
    ],
    'web/pages/playerinfo_general.php' => [
        'playerinfo_general.rank.hidden'   => '"',
        'playerinfo_general.rank.excluded' => '"',
    ],
    'web/pages/game.php' => [
        'game.range.24h'        => '"',
        'game.range.last_week'  => '"',
        'game.range.last_month' => '"',
        'game.range.last_year'  => '"',
    ],
];

/**
 * Removes one occurrence of each authorized-insertion block for $path
 * from $text (if present), returning the stripped text plus the list of
 * blocks actually found and removed.
 *
 * @return array{0: string, 1: string[]}
 */
function stripAuthorizedInsertions(string $path, string $text): array
{
    $blocks = AUTHORIZED_INSERTIONS[$path] ?? [];
    $removed = [];

    foreach ($blocks as $block) {
        $pos = strpos($text, $block);

        if ($pos !== false) {
            $text = substr_replace($text, '', $pos, strlen($block));
            $removed[] = $block;
        }
    }

    return [$text, $removed];
}

// This repo checks out CRLF (core.autocrlf=true) but git blobs (what
// `git show ref:path` returns) are LF-normalized. Normalize both sides so
// the comparison is about content, not EOL style.
$current = str_replace("\r\n", "\n", file_get_contents($currentPath));
$before = str_replace("\r\n", "\n", file_get_contents($args['before']));
$en = require $root . '/web/lang/en.php';

[$reconstructed, $wraps, $errors] = reconstructAsEn($current, $en, $args['path']);

foreach ($errors as $error) {
    echo "ERROR: {$error}\n";
}

if ($errors) {
    exit(1);
}

[$forCompare, $authorizedInsertions] = stripAuthorizedInsertions($args['path'], $reconstructed);

if ($forCompare === $before) {
    echo 'OK    ' . $args['path'] . ': ' . count($wraps) . " wrap(s), reconstructed en text matches baseline byte-for-byte.\n";
    foreach ($wraps as $key) {
        echo "      - {$key}\n";
    }
    foreach ($authorizedInsertions as $block) {
        echo '      + (authorized insertion) ' . trim($block) . "\n";
    }
    exit(0);
}

echo 'FAIL  ' . $args['path'] . ": reconstructed en text does NOT match baseline.\n";
echo diffPreview($before, $forCompare);
exit(1);

// -- helpers --------------------------------------------------------------

function parseArgs(array $argv): array
{
    $out = [];

    foreach (array_slice($argv, 1) as $arg) {
        if (substr($arg, 0, strlen('--before=')) === '--before=') {
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
function reconstructAsEn(string $source, array $en, string $path): array
{
    $wraps = [];
    $errors = [];
    $quoteOverrides = BARE_CALL_QUOTE_OVERRIDES[$path] ?? [];

    // Shape 1: the "<?=" short-echo wrap (must run before Shape 2/3, which
    // would otherwise also match the __('key') sitting inside that tag).
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

    // Shape 2 + 3: find every remaining literal or __('key') call using
    // PHP's own tokenizer, not regex. An earlier regex-only version of
    // this function matched quote characters textually, so an apostrophe
    // inside a // comment (e.g. "doesn't") was mistaken for a string
    // delimiter and corrupted quote-pairing for the rest of the file.
    // token_get_all() classifies comments (T_COMMENT) and interpolated
    // strings (which decompose into several tokens, not one
    // T_CONSTANT_ENCAPSED_STRING) correctly, so that class of bug can't
    // recur here.
    $tokens = token_get_all($source);
    $n = count($tokens);

    // Pass 1: locate atoms (literals and __('key') calls) with their
    // byte offsets and exact source text, plus whether a "connector"
    // (whitespace or a lone ".") separates each atom from the previous one.
    $atoms = [];
    $offset = 0;
    $i = 0;

    while ($i < $n) {
        $token = $tokens[$i];
        $id = is_array($token) ? $token[0] : null;
        $text = is_array($token) ? $token[1] : $token;

        if ($id === T_STRING && $text === '__') {
            $call = tryParseCall($tokens, $i);

            if ($call !== null) {
                [$consumed, $key] = $call;
                $callText = tokensText($tokens, $i, $consumed);
                $atoms[] = ['type' => 'call', 'text' => $callText, 'offset' => $offset, 'key' => $key];
                $offset += strlen($callText);
                $i += $consumed;
                continue;
            }
        }

        if ($id === T_CONSTANT_ENCAPSED_STRING) {
            $atoms[] = ['type' => 'literal', 'text' => $text, 'offset' => $offset, 'key' => null];
            $offset += strlen($text);
            $i++;
            continue;
        }

        $isConnector = $id === T_WHITESPACE || (!is_array($token) && $token === '.');
        $atoms[] = ['type' => $isConnector ? 'connector' : 'other', 'text' => $text, 'offset' => $offset, 'key' => null];
        $offset += strlen($text);
        $i++;
    }

    // Pass 2: group maximal runs of literal/call atoms separated only by
    // connector atoms into chains; anything else (code, comments, other
    // punctuation) breaks a chain.
    $chains = [];
    $current = [];
    $pendingConnectors = [];

    foreach ($atoms as $atom) {
        if ($atom['type'] === 'connector') {
            $pendingConnectors[] = $atom;
            continue;
        }

        if ($atom['type'] === 'literal' || $atom['type'] === 'call') {
            if ($current !== []) {
                $current = array_merge($current, $pendingConnectors);
            }
            $pendingConnectors = [];
            $current[] = $atom;
            continue;
        }

        // 'other': breaks the chain (pending connectors belonged to code
        // we're not touching, e.g. "$a, $b" -- discard them, not part of
        // any chain either way).
        if ($current !== []) {
            $chains[] = $current;
        }
        $current = [];
        $pendingConnectors = [];
    }

    if ($current !== []) {
        $chains[] = $current;
    }

    // Splice back-to-front so earlier offsets stay valid as we edit $source.
    for ($c = count($chains) - 1; $c >= 0; $c--) {
        $chain = $chains[$c];
        $hasCall = false;

        foreach ($chain as $atom) {
            if ($atom['type'] === 'call') {
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

        foreach ($chain as $atom) {
            if ($atom['type'] === 'connector') {
                continue; // "." and whitespace vanish once folded into one literal
            }

            if ($atom['type'] === 'call') {
                $key = $atom['key'];
                $chainKeys[] = $key;

                if (!array_key_exists($key, $en)) {
                    $chainError = "key '{$key}' has no entry in en.php";
                    break;
                }

                $value .= $en[$key];
            } else {
                $quoteChar ??= $atom['text'][0];
                $value .= decodePhpStringLiteral($atom['text']);
            }
        }

        $wraps = array_merge($wraps, $chainKeys);

        if ($chainError !== null) {
            $errors[] = $chainError;
            continue;
        }

        if ($quoteChar === null && count($chainKeys) === 1) {
            $quoteChar = $quoteOverrides[$chainKeys[0]] ?? null;
        }

        $first = $chain[0];
        $last = $chain[count($chain) - 1];
        $start = $first['offset'];
        $end = $last['offset'] + strlen($last['text']);

        $replacement = encodePhpStringLiteral($value, $quoteChar ?? "'");
        $source = substr_replace($source, $replacement, $start, $end - $start);
    }

    return [$source, $wraps, $errors];
}

/**
 * Matches T_STRING("__") "(" [T_WHITESPACE] T_CONSTANT_ENCAPSED_STRING [T_WHITESPACE] ")"
 * starting at $tokens[$i] (which must already be the "__" T_STRING).
 *
 * @return array{0: int, 1: string}|null [tokens consumed, key] or null if not a match.
 */
function tryParseCall(array $tokens, int $i): ?array
{
    $n = count($tokens);
    $j = $i + 1;

    if ($j >= $n || $tokens[$j] !== '(') {
        return null;
    }
    $j++;

    while ($j < $n && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
        $j++;
    }

    if ($j >= $n || !is_array($tokens[$j]) || $tokens[$j][0] !== T_CONSTANT_ENCAPSED_STRING) {
        return null;
    }

    $key = decodePhpStringLiteral($tokens[$j][1]);
    $j++;

    while ($j < $n && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
        $j++;
    }

    if ($j >= $n || $tokens[$j] !== ')') {
        return null;
    }

    return [$j - $i + 1, $key];
}

function tokensText(array $tokens, int $start, int $count): string
{
    $out = '';

    for ($k = 0; $k < $count; $k++) {
        $token = $tokens[$start + $k];
        $out .= is_array($token) ? $token[1] : $token;
    }

    return $out;
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
