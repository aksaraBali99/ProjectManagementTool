#!/usr/bin/env bash
#
# task #124: a release's notes entry as Markdown, for its GitHub Release
# (deploy.yml puts it above the "Tasks in this release" list).
#
# Usage: bash release-notes-body.sh <notes.json> <version>
#
# Prints "## Release notes" with the entry's summary and each task (task #N,
# title, and what changes for people), grouped by type. Prints nothing
# when there's no entry for <version>, and nothing (with a ::warning:: on
# stderr) when the entry can't be read: never a partial section. Exits 2
# when jq is missing.

set -euo pipefail
# shellcheck disable=SC1091 # release-notes-lib.sh, next to this script (checked on its own)
source "$(dirname "${BASH_SOURCE[0]}")/release-notes-lib.sh"

main() {
    if (($# != 2)); then
        echo "usage: release-notes-body.sh <notes.json> <version>" >&2
        return 2
    fi
    require_jq

    local body
    # shellcheck disable=SC2016 # $version, $entry, ... are jq's own variables
    if ! body=$(jqr -r --arg version "$2" '
        (.releases // [] | map(select(.version == $version)) | first) as $entry
        | if $entry == null then empty else
            "## Release notes", "", $entry.summary, "",
            ( ["Enhancement", "Bugfix", "Chore"][] as $type
              | [$entry.tasks[] | select(.type == $type)] as $tasks
              | select($tasks | length > 0)
              | "### \($type)", "",
                ( $tasks[] | "- **task #\(.number)** \(.title)" + (if (.after // "") != "" then ": " + (.after | gsub("\n"; " ")) else "" end) ),
                "" )
          end
    ' "$1" 2>/dev/null); then
        echo "::warning::Release notes: the entry for $2 couldn't be read, so the GitHub Release has no notes section." >&2
        return 0
    fi

    if [[ -n "$body" ]]; then
        printf '%s\n' "$body"
    fi
}

main "$@"
