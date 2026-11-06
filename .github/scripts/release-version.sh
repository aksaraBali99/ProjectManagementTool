#!/usr/bin/env bash
#
# task #124: the tag for a PROD deploy (deploy.yml).
#
# Usage: git tag --list | bash release-version.sh <notes.json> <YYYY.MM> <previous-tag|''>
#
# Prints the version of the newest entry in <notes.json> when it's a
# vYYYY.MM.N, isn't already a tag, and is higher than <previous-tag> (any
# version is, when there's no previous tag). Otherwise prints
# next-version.sh's version for <YYYY.MM>, with a ::warning:: on stderr
# saying why. Exits 2 when jq is missing.

set -euo pipefail
scripts="$(dirname "${BASH_SOURCE[0]}")"
# shellcheck disable=SC1091 # release-notes-lib.sh, next to this script (checked on its own)
source "$scripts/release-notes-lib.sh"

main() {
    if (($# != 3)); then
        echo "usage: git tag --list | release-version.sh <notes.json> <YYYY.MM> <previous-tag|''>" >&2
        return 2
    fi
    require_jq

    local notes="$1" month="$2" previous="$3" tags
    tags=$(tr -d '\r')

    local version="" reason=""
    if ! version=$(jqr -r '.releases[0].version // ""' "$notes" 2>/dev/null); then
        version=""
        reason="$notes isn't valid JSON"
    elif [[ -z "$version" ]]; then
        reason="$notes has no entry"
    elif [[ -z "$(version_key "$version")" ]]; then
        reason="the newest entry's version \"$version\" isn't vYYYY.MM.N"
    elif grep -qxF "$version" <<<"$tags"; then
        reason="the newest entry's version $version is already a tag"
    elif [[ -n "$previous" && ! "$(version_key "$version")" > "$(version_key "$previous")" ]]; then
        reason="the newest entry's version $version isn't higher than the previous tag $previous"
    fi

    if [[ -z "$reason" ]]; then
        echo "$version"
        return 0
    fi

    local fallback
    fallback=$(bash "$scripts/next-version.sh" "$month" <<<"$tags")
    echo "::warning::Release notes: $reason, so this release is tagged $fallback (next-version.sh) instead. Fix the entry's version on main afterwards." >&2
    echo "$fallback"
}

main "$@"
