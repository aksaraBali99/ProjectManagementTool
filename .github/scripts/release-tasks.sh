#!/usr/bin/env bash
#
# task #119: the tasks in a release, as a Markdown list.
#
# Usage: bash release-tasks.sh <previous-tag> <new-sha>
#
# Lists each "task #N" found in the commit subjects after <previous-tag>
# up to <new-sha> (git log --format=%s), once, sorted by number. With an
# empty <previous-tag>, every commit up to <new-sha> counts. Subjects
# without a task number are skipped. Run inside the repository.

set -euo pipefail

main() {
    if (($# != 2)) || [[ -z "$2" ]]; then
        echo "usage: release-tasks.sh <previous-tag|''> <new-sha>" >&2
        return 2
    fi

    local previous="$1" new="$2" range numbers
    if [[ -n "$previous" ]]; then
        range="${previous}..${new}"
    else
        range="$new"
    fi

    numbers=$(git log --format=%s "$range" \
        | grep -oiE 'task #[0-9]+' \
        | grep -oE '[0-9]+' \
        | sort -n -u || true)

    if [[ -z "$numbers" ]]; then
        echo "- None found in the commit subjects"
        return 0
    fi

    local number
    while IFS= read -r number; do
        echo "- Task #${number}"
    done <<<"$numbers"
}

main "$@"
