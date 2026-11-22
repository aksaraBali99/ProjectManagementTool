#!/usr/bin/env bash
#
# SOL-0023: the forced command for Solava's SSH deploy keys. Lives on the
# server as /home/u738067934/apps/solava/bin/gate.sh (folder and file 700),
# copied there by hand (docs/release-process.md, "SSH deploy gate"). Each
# key's line in ~/.ssh/authorized_keys binds it to one environment:
#
#   command="/home/u738067934/apps/solava/bin/gate.sh dev",restrict ssh-ed25519 AAAA... solava-gate-dev
#
# sshd runs this instead of whatever the client asked for, and passes the
# client's command in SSH_ORIGINAL_COMMAND. Exactly one of these is run:
#
#   ping                          prints "ok solava-gate <env>"
#   deploy <40-hex sha>           runs that commit's deploy.sh
#   deploy <40-hex sha> --dry-run the same, with deploy.sh --dry-run
#
# Anything else (a login shell, scp/sftp, a second command) is refused.
# Where to deploy, which branch, which APP_ENV and PROD's --maintenance
# --backup are fixed below, never taken from the client.
#
# Exit codes: deploy.sh's own (0 done, 78 skipped: newer commit, 1 failed),
# 126 refused, 75 another deploy is running, 1 the commit isn't on the
# branch.
#
# umask is never set for the whole script: deploy.sh inherits it, and a
# 077 umask makes every file git writes unreadable by the web server. Only
# the gate's own files (log, lock, temp script) are made private, one by
# one.

set -euo pipefail

# --- Settings, per environment ------------------------------------------
# A gate.conf next to this script may override the path, branch and
# APP_ENV (the tests do; the server needs none). It's read only when it's
# owned by this user and nobody else can write to it. The deploy.sh flags
# are set after it, so PROD always gets --maintenance --backup.

dev_app_path=/home/u738067934/domains/solavadev.aksaravirtualagency.com/solava
dev_branch=main
dev_app_env=dev

production_app_path=/home/u738067934/domains/solava.aksaravirtualagency.com/solava
production_branch=production
production_app_env=production

# ---------------------------------------------------------------------------

exec </dev/null

gate_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd -P)"
log_dir="$gate_dir/../logs"
run_dir="$gate_dir/../run"
log_file="$log_dir/gate.log"
conf="$gate_dir/gate.conf"

raw_command="${SSH_ORIGINAL_COMMAND:-}"
env_label="${1:-}"
verdict=refused
is_deploy=0
lock=""
lock_held=0
tmp=""

# One line per attempt, written on the way out (finish, below). Control
# characters stripped and cut to 200, so a command can't fake a second
# line. Never environment variables. Best effort: a full disk mustn't
# block a deploy.
write_log() {
    local code="$1" cmd ip line
    cmd="${raw_command//[[:cntrl:]]/}"
    cmd="${cmd:0:200}"
    ip="${SSH_CLIENT:-}"
    ip="${ip%% *}"
    ip="${ip//[^0-9a-fA-F.:]/}"
    local label="${env_label//[^a-z]/}"
    line="$(date -u +%Y-%m-%dT%H:%M:%SZ) env=${label:0:20} ip=${ip:--} verdict=${verdict}"
    if ((is_deploy)); then line+=" exit=${code}"; fi
    line+=" cmd=${cmd}"
    {
        (umask 077 && mkdir -p "$log_dir")
        if [[ -f "$log_file" ]] && (($(wc -c <"$log_file") > 1048576)); then
            mv -f "$log_file" "$log_file.1"
        fi
        (umask 077 && printf '%s\n' "$line" >>"$log_file")
        chmod 600 "$log_file"
    } 2>/dev/null || true
}

# Every way out goes through here: called directly, and from the traps
# for an error under set -e or a signal (an SSH disconnect is HUP). It
# removes the temp script and the lock (only if it's still ours), writes
# the log line once, and exits with the code.
finished=0
finish() {
    local code="$1"
    if ((finished)); then exit "$code"; fi
    finished=1
    if [[ -n "$tmp" ]]; then rm -f -- "$tmp"; fi
    if ((lock_held)) && [[ "$(cat "$lock/pid" 2>/dev/null || true)" == "$$" ]]; then
        rm -f -- "$lock/pid"
        rmdir -- "$lock" 2>/dev/null || true
    fi
    write_log "$code"
    exit "$code"
}
trap 'finish $?' EXIT
trap 'finish 129' HUP
trap 'finish 130' INT
trap 'finish 143' TERM

refuse() {
    verdict=refused
    echo "refused${1:+: $1}" >&2
    finish 126
}

# --- The environment, from the key's line --------------------------------

if (($# != 1)); then refuse "the key's line must give exactly one environment"; fi
case "$env_label" in
    dev | production) ;;
    *) refuse "unknown environment" ;;
esac

if [[ -e "$conf" || -L "$conf" ]]; then
    if [[ -L "$conf" || ! -f "$conf" || ! -O "$conf" ]]; then
        refuse "gate.conf isn't a plain file owned by this user"
    fi
    conf_mode=$(stat -c %a "$conf")
    if ((8#$conf_mode & 8#022)); then
        refuse "gate.conf can be written by others (mode $conf_mode); chmod 600 it"
    fi
    # shellcheck source=/dev/null
    source "$conf"
fi

if [[ "$env_label" == "production" ]]; then
    app_path="$production_app_path"
    branch="$production_branch"
    app_env="$production_app_env"
    flags=(--maintenance --backup)
else
    app_path="$dev_app_path"
    branch="$dev_branch"
    app_env="$dev_app_env"
    flags=()
fi

# --- The command ----------------------------------------------------------
# A whole-string match: bash's =~ anchors ^ and $ to the whole value, so a
# newline can't add a second line. Nothing is evaluated or word-split.

# The hex digits are spelled out: a range like a-f can match more than six
# letters in some locales.
allowed='^(ping|deploy ([0123456789abcdef]{40})( --dry-run)?)$'
if [[ ! "$raw_command" =~ $allowed ]]; then
    refuse
fi

if [[ "$raw_command" == "ping" ]]; then
    verdict=accepted
    echo "ok solava-gate $env_label"
    finish 0
fi

sha="${BASH_REMATCH[2]}"
dry_run=()
if [[ -n "${BASH_REMATCH[3]}" ]]; then dry_run=(--dry-run); fi
is_deploy=1

# --- One deploy at a time per environment --------------------------------
# mkdir is atomic, and flock isn't always installed on shared hosting. A
# lock whose pid is gone is taken over; one with no pid file yet (killed
# between mkdir and writing it) only after 5 minutes.

(umask 077 && mkdir -p "$run_dir")
lock="$run_dir/$env_label.lock"

lock_is_stale() {
    local pid age
    if [[ ! -d "$lock" ]]; then return 0; fi
    pid=$(cat "$lock/pid" 2>/dev/null || true)
    if [[ "$pid" =~ ^[0-9]+$ ]]; then
        if kill -0 "$pid" 2>/dev/null; then return 1; fi
        return 0
    fi
    age=$(($(date +%s) - $(stat -c %Y "$lock" 2>/dev/null || date +%s)))
    ((age > 300))
}

for _ in 1 2; do
    if mkdir "$lock" 2>/dev/null; then
        chmod 700 "$lock"
        (umask 077 && printf '%s\n' "$$" >"$lock/pid")
        lock_held=1
        break
    fi
    if ! lock_is_stale; then break; fi
    echo "Taking over a stale lock ($lock)." >&2
    rm -f -- "$lock/pid"
    rmdir -- "$lock" 2>/dev/null || true
done
if ((!lock_held)); then
    verdict=busy
    is_deploy=0
    echo "refused: another deploy is running on $env_label" >&2
    finish 75
fi
verdict=accepted

# --- Deploy ----------------------------------------------------------------

cd "$app_path"
git fetch --quiet origin "$branch"

# Nothing from a commit that isn't on the branch is ever run.
if ! git merge-base --is-ancestor "$sha" "origin/$branch" 2>/dev/null; then
    echo "refused: $sha isn't on origin/$branch. Nothing was run." >&2
    finish 1
fi

# The commit's own deploy.sh, in a private temp file. Not `bash -s`:
# composer or artisan reading stdin would eat the script.
tmp=$(mktemp "$run_dir/deploy.XXXXXXXX")
chmod 600 "$tmp"
git show "$sha:deploy.sh" >"$tmp"

set +e
bash "$tmp" --branch "$branch" --expect-sha "$sha" --expect-env "$app_env" "${flags[@]}" "${dry_run[@]}"
code=$?
set -e
finish "$code"
