#!/usr/bin/env bash
#
# task #119: the tasks in a release, as a Markdown list. SOL-0021: task
# codes (SOL-0021) as well as the old global numbers (task #N).
#
# Usage: bash release-tasks.sh [--ids] <previous-tag|''> <new-sha>
#
# Reads the commit subjects after <previous-tag> up to <new-sha> (git log
# --format=%s; with an empty <previous-tag>, every commit up to <new-sha>)
# and lists each task in them once:
#   - A task code (3 letters, a hyphen, 4 or more digits: App\Support\
#     CompanyCode and Task::formatCode()) that is a whole comma-separated
#     item inside parentheses: "(SOL-0021)", "(SOL-0021, phase 1/3)",
#     "(SOL-0021, SOL-0030)". Not in running text, and not "(ISO-8601)" or
#     "(UTF-8)". Uppercased; the digits kept as written.
#   - The branch of a GitHub merge commit, "Merge pull request #N from
#     <owner>/<Type>/<CODE>-...", with <Type> Enhancement, Bugfix, Chore or
#     Hotfix: catches a PR whose own commits left the code out.
#   - An old "task #N", any case, anywhere in a subject (releases up to
#     v2026.11.1).
#
# Prints codes first (by prefix, then by number), then old numbers
# ascending: "- SOL-0021", "- Task #118", or "- None found in the commit
# subjects" when there are none. With --ids, one id per line instead
# ("SOL-0021", "#118"), and nothing at all when there are none: the tasks
# file release-notes-check.sh compares with. Run inside the repository.

set -euo pipefail

usage() {
    echo "usage: release-tasks.sh [--ids] <previous-tag|''> <new-sha>" >&2
}

# Codes that are a whole item of a parenthesised group, one per line.
codes_in_parentheses() {
    grep -oE '\([^()]*\)' \
        | sed -e 's/^(//' -e 's/)$//' \
        | tr ',' '\n' \
        | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' \
        | grep -E '^[A-Za-z]{3}-[0-9]{4,}$' \
        | tr '[:lower:]' '[:upper:]' || true
}

# The code in a merged PR's branch name, one per line.
codes_in_merge_branches() {
    sed -nE 's~^Merge pull request #[0-9]+ from [^/[:space:]]+/(Enhancement|Bugfix|Chore|Hotfix)/([A-Za-z]{3}-[0-9]{4,})(-.*)?$~\2~p' \
        | tr '[:lower:]' '[:upper:]' || true
}

main() {
    local ids_only=0
    if [[ "${1:-}" == "--ids" ]]; then
        ids_only=1
        shift
    fi
    if (($# != 2)) || [[ -z "$2" ]]; then
        usage
        return 2
    fi

    local previous="$1" new="$2" range subjects codes numbers
    if [[ -n "$previous" ]]; then
        range="${previous}..${new}"
    else
        range="$new"
    fi
    subjects=$(git log --format=%s "$range")

    # Unique first, then ordered: a numeric sort key alone would treat
    # SOL-0021 and SOL-00021 as one.
    codes=$({ codes_in_parentheses <<<"$subjects"; codes_in_merge_branches <<<"$subjects"; } \
        | LC_ALL=C sort -u | LC_ALL=C sort -t- -k1,1 -k2,2n)
    numbers=$(grep -oiE 'task #[0-9]+' <<<"$subjects" | grep -oE '[0-9]+' | sort -n -u || true)

    local code number
    if ((ids_only)); then
        [[ -n "$codes" ]] && printf '%s\n' "$codes"
        if [[ -n "$numbers" ]]; then
            while IFS= read -r number; do echo "#${number}"; done <<<"$numbers"
        fi
        return 0
    fi

    if [[ -z "$codes" && -z "$numbers" ]]; then
        echo "- None found in the commit subjects"
        return 0
    fi
    if [[ -n "$codes" ]]; then
        while IFS= read -r code; do echo "- ${code}"; done <<<"$codes"
    fi
    if [[ -n "$numbers" ]]; then
        while IFS= read -r number; do echo "- Task #${number}"; done <<<"$numbers"
    fi
}

main "$@"
