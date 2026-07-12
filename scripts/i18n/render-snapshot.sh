#!/usr/bin/env bash
#
# Render-snapshot parity harness (FEAT-0027-PLAN §6.3, the primary/decisive
# en-parity gate). Fetches a fixed set of page URLs from a running instance
# and writes normalized HTML dumps to a directory, one file per URL.
#
# Purpose: run this against a pre-i18n checkout and again against a post-i18n
# checkout (both served from `php -S` against the SAME seeded database, both
# with hlstats_Options.language = 'en'), then diff the two output directories
# byte-for-byte. An empty diff proves the wrap-and-resolve pipeline reproduces
# the original English output exactly -- the argument token-diff/catalog-lint
# cannot make on their own, because a symbolic key pointing at the WRONG (but
# syntactically valid) en.php value would pass those static checks and still
# render wrong.
#
# Usage:
#   render-snapshot.sh <BASE_URL> <OUTPUT_DIR> [PAGE_LIST_FILE]
#
#   BASE_URL       e.g. http://127.0.0.1:8081  (no trailing slash)
#   OUTPUT_DIR     directory to write normalized *.html dumps into (created
#                  if missing; existing contents are left alone -- point at
#                  a fresh directory per run if you don't want stale files)
#   PAGE_LIST_FILE defaults to snapshot-pages.txt next to this script
#
# Typical two-run comparison:
#   # pre-i18n checkout, served on :8081
#   scripts/i18n/render-snapshot.sh http://127.0.0.1:8081 /tmp/snap-before
#   # post-i18n checkout, served on :8082, same DB, language='en'
#   scripts/i18n/render-snapshot.sh http://127.0.0.1:8082 /tmp/snap-after
#   diff -rq /tmp/snap-before /tmp/snap-after   # must print nothing
#
# Prerequisites this script does NOT set up for you (by design -- it only
# renders and normalizes, it doesn't own environment/DB lifecycle):
#   - A PHP built-in server already running at BASE_URL
#     (php -S 127.0.0.1:8081 -t web  -- or wherever the docroot is)
#   - web/config.php filled in with real DB_ADDR/DB_USER/DB_PASS/DB_NAME
#     pointing at your seeded database (the checked-in config.php is a safe
#     template with empty credentials -- never commit real ones)
#   - hlstats_Options.language = 'en' in that seeded database for both runs
#
# Fail-fast: the first URL that doesn't come back with HTTP 200 aborts the
# whole run with a non-zero exit code and names the failing URL. This is
# intentional -- a partial snapshot set is worse than none, since a missing
# file reads as "no diff" rather than "couldn't check."

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

BASE_URL="${1:-}"
OUTPUT_DIR="${2:-}"
PAGE_LIST_FILE="${3:-$SCRIPT_DIR/snapshot-pages.txt}"

if [ -z "$BASE_URL" ] || [ -z "$OUTPUT_DIR" ]; then
    echo "Usage: $0 <BASE_URL> <OUTPUT_DIR> [PAGE_LIST_FILE]" >&2
    echo "  e.g.: $0 http://127.0.0.1:8081 /tmp/snap-before" >&2
    exit 1
fi

if [ ! -f "$PAGE_LIST_FILE" ]; then
    echo "ERROR: page list file not found: $PAGE_LIST_FILE" >&2
    exit 1
fi

# Strip a trailing slash from BASE_URL so "$BASE_URL$path" never double-slashes.
BASE_URL="${BASE_URL%/}"

mkdir -p "$OUTPUT_DIR"

# Turns a URL path+query into a filesystem-safe filename, e.g.
#   /hlstats.php?mode=players&game=l4d2  ->  mode=players&game=l4d2.html
# (leading "/hlstats.php?" is dropped since every URL in the list shares it;
# the bare "/" landing page becomes "_root.html").
path_to_filename() {
    local p="$1"
    p="${p#/hlstats.php}"
    p="${p#\?}"
    if [ -z "$p" ]; then
        echo "_root.html"
        return
    fi
    # Replace filesystem-hostile characters with underscores.
    echo "$(echo "$p" | sed 's/[\/\\:*?"<>|]/_/g').html"
}

# Normalizes known non-deterministic content so that two snapshots of
# genuinely-identical pages compare equal even if fetched at different wall-
# clock moments or via different host:port. Extend this if a real snapshot
# run surfaces more noise (documented candidates below).
normalize_body() {
    sed \
        -e "s#$(printf '%s' "$BASE_URL" | sed 's/[.[\*^$/]/\\&/g')#{{BASE_URL}}#g" \
        -e 's/[0-9][0-9]:[0-9][0-9]:[0-9][0-9]/{{HH:MM:SS}}/g' \
        -e 's/Executed [0-9]\+ queries, generated this page in [0-9.]\+ Seconds/{{QUERY_DEBUG_LINE}}/g'
    # Known un-normalized residual randomness (not handled above -- avoid by
    # seeding with $g_options['show_weapon_target_flash'] = 0 instead of
    # trying to regex it away): claninfo_weapons.php/playerinfo_weapons.php's
    # flash hitbox branch picks a random player-model skin via array_rand()
    # on every request, independent of language/i18n.
}

count=0
while IFS= read -r line || [ -n "$line" ]; do
    # Strip inline "# comment" (everything from the first '#' onward), then
    # trim surrounding whitespace.
    url_path="${line%%#*}"
    url_path="$(echo "$url_path" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"

    # Skip blank lines and full-line comments.
    [ -z "$url_path" ] && continue

    filename="$(path_to_filename "$url_path")"
    full_url="${BASE_URL}${url_path}"

    body="$(curl -sS --fail --max-time 30 "$full_url")" || {
        echo "ERROR: request failed for $full_url" >&2
        echo "       (page list entry: $url_path)" >&2
        exit 1
    }

    printf '%s' "$body" | normalize_body > "$OUTPUT_DIR/$filename"
    count=$((count + 1))
done < "$PAGE_LIST_FILE"

echo "OK: wrote $count normalized snapshot(s) to $OUTPUT_DIR"
