#!/usr/bin/env bash
#
# task #119: checks an environment's deploy config before anything is sent
# to the server (the deploy-over-ssh action's first step).
#
# Usage: bash check-deploy-config.sh
#
# Reads SSH_HOST, SSH_PORT, SSH_USER, APP_PATH, APP_URL, EXPECTED_APP_ENV,
# SSH_KNOWN_HOSTS and the key from the environment: SSH_GATE_KEY when
# SSH_GATE is 'true' (SOL-0023, the restricted key for the server's deploy
# gate), else SSH_KEY. Fails when any is missing, or when APP_PATH isn't a
# full path: the deploy quotes it, so a "~" or a relative path would never
# resolve on the server.

set -euo pipefail

main() {
    local key=SSH_KEY
    if [[ "${SSH_GATE:-}" == "true" ]]; then key=SSH_GATE_KEY; fi

    local missing=() name
    for name in SSH_HOST SSH_PORT SSH_USER APP_PATH APP_URL EXPECTED_APP_ENV "$key" SSH_KNOWN_HOSTS; do
        if [[ -z "${!name:-}" ]]; then missing+=("$name"); fi
    done
    if [[ " ${missing[*]} " == *" SSH_GATE_KEY "* ]]; then
        echo "::error::SSH_GATE is on, but SSH_GATE_KEY isn't set. Add the gate key as this environment's SSH_GATE_KEY secret, or set SSH_GATE to false to deploy with SSH_KEY (docs/release-process.md, SSH deploy gate)."
    fi
    if ((${#missing[@]} > 0)); then
        echo "::error::Missing config for this environment: ${missing[*]}. Add them under Settings > Environments (docs/release-process.md, one-time setup)."
        return 1
    fi

    if [[ "$APP_PATH" != /* ]]; then
        echo "::error::APP_PATH must be the full path on the server, starting with /home/ (e.g. /home/u123456789/domains/<site>/solava). A path with ~ or a relative path doesn't work, because the deploy quotes it. Run pwd in the app folder over SSH to get it."
        return 1
    fi
}

main "$@"
