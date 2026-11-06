#!/usr/bin/env bash
#
# task #119: the next release tag, vYYYY.MM.N.
#
# Usage: git tag --list | bash next-version.sh 2026.11
#
# Reads the existing tags on stdin and prints v<year-month>.<N>, where N is
# 1 + the highest N among the tags for that year and month (1 if there are
# none). Tags that don't match vYYYY.MM.N exactly are ignored. The caller
# picks the year-month (Asia/Makassar, see deploy.yml).

set -euo pipefail

main() {
    local month="${1:-}"
    if [[ ! "$month" =~ ^[0-9]{4}\.[0-9]{2}$ ]]; then
        echo "usage: next-version.sh YYYY.MM < tags" >&2
        return 2
    fi

    local highest=0 tag number
    while IFS= read -r tag; do
        tag="${tag%$'\r'}"
        if [[ "$tag" =~ ^v([0-9]{4}\.[0-9]{2})\.([0-9]+)$ ]] && [[ "${BASH_REMATCH[1]}" == "$month" ]]; then
            # 10# so a leading zero isn't read as octal.
            number=$((10#${BASH_REMATCH[2]}))
            if ((number > highest)); then
                highest=$number
            fi
        fi
    done

    echo "v${month}.$((highest + 1))"
}

main "$@"
