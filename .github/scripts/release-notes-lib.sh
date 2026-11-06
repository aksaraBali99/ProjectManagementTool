#!/usr/bin/env bash
#
# task #124: helpers shared by the release-notes scripts. Sourced, not run.

# jq, required; its output with any Windows \r removed, so the same scripts
# run on a Windows machine and on the Linux runners.
require_jq() {
    if ! command -v jq >/dev/null 2>&1; then
        echo "::error::jq is required (preinstalled on GitHub's ubuntu runners; locally: winget install jqlang.jq, or apt/brew install jq)." >&2
        exit 2
    fi
}

jqr() {
    jq "$@" | tr -d '\r'
}

# A sortable key for vYYYY.MM.N (numbers, so .10 sorts after .9), or
# nothing for anything else.
version_key() {
    if [[ "$1" =~ ^v([0-9]{4})\.([0-9]{2})\.([0-9]+)$ ]]; then
        printf '%04d%02d%09d' "$((10#${BASH_REMATCH[1]}))" "$((10#${BASH_REMATCH[2]}))" "$((10#${BASH_REMATCH[3]}))"
    fi
}
