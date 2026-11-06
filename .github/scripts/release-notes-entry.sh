#!/usr/bin/env bash
#
# task #124: which release notes entry belongs to a PROD deploy
# (deploy.yml), for its GitHub Release.
#
# Usage: git tag --list | bash release-notes-entry.sh <notes.json>
#
# Prints the newest entry's version when it isn't already a tag: those
# notes are this release's, even when the deploy had to tag another
# version (release-version.sh's fallback). Prints nothing, with a
# ::warning:: on stderr, when it's already a tag (a stale entry from an
# earlier release: better no notes than the wrong ones) or there's no
# usable entry. Exits 2 when jq is missing.

set -euo pipefail
# shellcheck disable=SC1091 # release-notes-lib.sh, next to this script (checked on its own)
source "$(dirname "${BASH_SOURCE[0]}")/release-notes-lib.sh"

main() {
    if (($# != 1)); then
        echo "usage: git tag --list | release-notes-entry.sh <notes.json>" >&2
        return 2
    fi
    require_jq

    local tags version
    tags=$(tr -d '\r')

    if ! version=$(jqr -r '.releases[0].version // ""' "$1" 2>/dev/null) || [[ -z "$(version_key "$version")" ]]; then
        echo "::warning::Release notes: no usable entry in $1, so the GitHub Release has no notes section." >&2
        return 0
    fi

    if grep -qxF "$version" <<<"$tags"; then
        echo "::warning::Release notes: the newest entry, $version, is already a tag (an earlier release's), so the GitHub Release has no notes section. Add this release's entry on main." >&2
        return 0
    fi

    echo "$version"
}

main "$@"
