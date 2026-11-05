#!/usr/bin/env bash
#
# task #119: the source and checklist checks for a pull request into
# production (release-check.yml).
#
# Usage: PR_BODY="<the PR description>" bash release-check.sh <head-branch>
#
# - Source: the head branch must be main (a release) or start with
#   Hotfix/ (a hotfix). Anything else fails.
# - Checklist, releases from main only: fails while the description still
#   has an unticked "- [ ]" box (and lists them), or has no ticked box at
#   all (the release template wasn't used).
#
# The description comes in through the PR_BODY environment variable, never
# pasted into a script, so nothing in it can run as code.

set -euo pipefail

main() {
    local head="${1:-}"
    if [[ -z "$head" ]]; then
        echo "usage: PR_BODY=... release-check.sh <head-branch>" >&2
        return 2
    fi

    if [[ "$head" == "main" ]]; then
        echo "Source: main (a release)."
    elif [[ "$head" == Hotfix/* ]]; then
        echo "Source: ${head} (a hotfix). The checklist check doesn't apply."
        return 0
    else
        echo "::error::Only main (a release) or a Hotfix/<task>-<slug> branch can be merged into production, not '${head}'."
        return 1
    fi

    local unticked
    unticked=$(printf '%s\n' "${PR_BODY:-}" | tr -d '\r' | grep -E '^[[:space:]]*[-*] \[ \]' || true)
    if [[ -n "$unticked" ]]; then
        echo "::error::The release checklist isn't finished. Tick every box before merging:"
        printf '%s\n' "$unticked"
        return 1
    fi

    # No boxes at all: the release template wasn't used.
    if ! printf '%s\n' "${PR_BODY:-}" | tr -d '\r' | grep -qiE '^[[:space:]]*[-*] \[x\]'; then
        echo "::error::No release checklist found. Open the release PR with the release template (see docs/release-process.md)."
        return 1
    fi

    echo "Checklist: every box is ticked."
}

main "$@"
