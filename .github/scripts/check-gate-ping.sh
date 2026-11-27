#!/usr/bin/env bash
#
# SOL-0023: checks the server's answer to "ping" before a gate deploy, so
# the key in SSH_GATE_KEY is known to be bound to this job's environment.
# The gate fixes where it deploys from the key's line, so GitHub can no
# longer send --expect-env: without this, the DEV gate key pasted into the
# production environment would send PROD's commit to DEV.
#
# Usage: bash check-gate-ping.sh <environment> <ssh exit code> <reply> [<ssh stderr>]
#
# Exits 0 when the reply is exactly "ok solava-gate <environment>", else
# prints an ::error:: saying what's wrong and exits 1. SOL-0029: when SSH
# (or the server) wrote anything to stderr, the error ends with it, so an
# exit 255 says why SSH failed. Tested by tests/scripts/run.sh.

set -euo pipefail

# one_line <text> <max length>: the text on one line, cut to the length,
# safe inside an ::error:: command. Control characters (newlines too)
# become spaces, so nothing in it can start a workflow command of its own,
# and % is escaped, so the runner can't turn %0A back into a newline.
one_line() {
    local text="${1//[[:cntrl:]]/ }"
    text="${text:0:$2}"
    printf '%s' "${text//%/%25}"
}

main() {
    local environment="$1" code="$2" reply="$3" stderr="${4:-}"

    if [[ "$code" == "0" && "$reply" == "ok solava-gate $environment" ]]; then
        echo "The server's gate answers for $environment."
        return 0
    fi

    # What SSH said on stderr, for the end of the message. The SSH client
    # writes no key material there (at most the host, user, port and
    # reason), but once a key logs in, the server's own stderr is in it
    # too: login scripts and the command's errors, e.g. with the old
    # unrestricted key in SSH_GATE_KEY. Its last 300 characters are kept,
    # since the reason usually comes after any banner or warnings.
    local ssh_said=""
    local trimmed="${stderr//[[:space:]]/}"
    if [[ -n "$trimmed" ]]; then
        local tail="$stderr"
        if ((${#tail} > 300)); then tail="...${tail: -300}"; fi
        ssh_said=" SSH said: '$(one_line "$tail" 303)'"
    fi

    local gate_answer='^ok solava-gate (dev|production)$'
    if [[ "$code" == "0" && "$reply" =~ $gate_answer ]]; then
        echo "::error::SSH_GATE_KEY in the '$environment' environment is the gate key for '${BASH_REMATCH[1]}', not '$environment'. Put the $environment gate key in this environment's SSH_GATE_KEY secret (docs/release-process.md, section 8). Nothing was deployed.$ssh_said"
        return 1
    fi

    # What the server said, printable and short, for the message.
    local shown
    shown="$(one_line "$reply" 100)"
    case "$code" in
        126)
            echo "::error::The server's gate refused even 'ping' (exit 126): the environment on this key's line in authorized_keys, or gate.conf next to gate.sh, is wrong. Check ~/apps/solava/logs/gate.log on the server (docs/release-process.md, section 8). Nothing was deployed.$ssh_said"
            ;;
        255)
            echo "::error::SSH itself failed (exit 255): the server is unreachable, its host key changed (SSH_KNOWN_HOSTS), or it doesn't accept SSH_GATE_KEY. Nothing was deployed.$ssh_said"
            ;;
        *)
            echo "::error::The server didn't answer 'ping' like the gate (exit $code, reply: '$shown'). This key's line in authorized_keys probably has no gate (command=\"/home/<user>/apps/solava/bin/gate.sh $environment\",restrict), or SSH_GATE_KEY holds the old unrestricted key. Fix the key line, or set SSH_GATE to false to deploy the old way (docs/release-process.md, section 8). Nothing was deployed.$ssh_said"
            ;;
    esac
    return 1
}

main "$@"
