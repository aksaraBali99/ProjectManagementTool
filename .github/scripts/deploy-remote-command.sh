#!/usr/bin/env bash
#
# task #119, SOL-0023: prints the command the deploy-over-ssh action sends
# to the server. Tested by tests/scripts/run.sh.
#
# Usage: bash deploy-remote-command.sh
#
# Reads SSH_GATE, BRANCH, SHA, DRY_RUN, DEPLOY_ARGS, APP_PATH and
# EXPECTED_APP_ENV from the environment.
#
# - SSH_GATE 'true': the short command the server's gate accepts
#   (server/solava-gate.sh): "deploy <sha>" or "deploy <sha> --dry-run".
#   DEPLOY_ARGS, APP_PATH and EXPECTED_APP_ENV aren't sent: the gate fixes
#   them, and adds PROD's --maintenance --backup itself.
# - Otherwise: the whole shell line, exactly as before the gate. $HOME
#   stays literal, so it expands on the server, not on the runner.

set -euo pipefail

main() {
    if [[ "${SSH_GATE:-}" == "true" ]]; then
        local remote="deploy $SHA"
        if [[ "$DRY_RUN" == "true" ]]; then remote+=" --dry-run"; fi
        printf '%s' "$remote"
        return
    fi

    local args=(--branch "$BRANCH" --expect-sha "$SHA" --expect-env "$EXPECTED_APP_ENV")
    # Word-split on purpose: a fixed list of flags from deploy.yml.
    local extra=()
    read -r -a extra <<<"$DEPLOY_ARGS"
    args+=("${extra[@]}")
    if [[ "$DRY_RUN" == "true" ]]; then args+=(--dry-run); fi

    # The commit's own deploy.sh, not the copy in the server's current
    # checkout (that's the previous release's). A temp file, not
    # `bash -s`: composer or artisan reading stdin would eat the script.
    local script="\$HOME/.deploy-${SHA}.sh" remote
    # shellcheck disable=SC2016 # $?, $code: expanded on the server, not here
    printf -v remote 'cd %q && git fetch --quiet origin %q && git show %q:deploy.sh > %s && bash %s %s; code=$?; rm -f %s; exit $code' \
        "$APP_PATH" "$BRANCH" "$SHA" "$script" "$script" "$(printf '%q ' "${args[@]}")" "$script"
    printf '%s' "$remote"
}

main "$@"
