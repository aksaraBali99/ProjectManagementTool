#!/usr/bin/env bash
#
# Solava deploy script. Runs on the SERVER, from the app folder (the
# "solava" folder, not public_html). The Deploy workflow
# (.github/workflows/deploy.yml) runs it over SSH; it can also be run by
# hand. See docs/release-process.md.
#
# Usage:
#   bash deploy.sh --branch main|production --expect-sha <sha> --expect-env <APP_ENV>
#                  [--maintenance] [--backup] [--dry-run]
#
#   --branch       the branch this server runs (main on DEV, production on PROD)
#   --expect-sha   the commit to deploy; origin/<branch> must be exactly it
#   --expect-env   this server's APP_ENV, so a deploy can't hit the wrong server
#   --maintenance  site down (php artisan down) from the pull until the caches are rebuilt
#   --backup       php artisan solva:db-backup before anything changes
#   --dry-run      print what would be deployed, change nothing
#
# By hand, for the commit at the tip of the branch:
#   git fetch origin main && bash deploy.sh --branch main --expect-sha "$(git rev-parse origin/main)" --expect-env <APP_ENV>
#
# Exit codes: 0 deployed (or dry run done), 78 skipped because a newer
# commit is on the branch, 2 bad arguments, 1 any other failure.
#
# This does NOT build frontend assets. Hostinger's shared hosting can't
# run npm, so public/build is built locally and committed.
#
# The whole script is one function, called on the last line: bash reads a
# script as it runs it, and this one pulls a new version of itself, so it
# must be fully read before anything changes.

main() {
    set -euo pipefail

    local skipped=78
    local branch="" expect_sha="" expect_env=""
    local maintenance=0 backup=0 dry_run=0

    while (($# > 0)); do
        case "$1" in
            --branch) branch="${2:-}"; shift 2 || usage ;;
            --expect-sha) expect_sha="${2:-}"; shift 2 || usage ;;
            --expect-env) expect_env="${2:-}"; shift 2 || usage ;;
            --maintenance) maintenance=1; shift ;;
            --backup) backup=1; shift ;;
            --dry-run) dry_run=1; shift ;;
            -h | --help) usage ;;
            *) echo "Unknown option: $1" >&2; usage ;;
        esac
    done

    if [[ "$branch" != "main" && "$branch" != "production" ]]; then
        echo "--branch must be main or production." >&2
        usage
    fi
    if [[ ! "$expect_sha" =~ ^[0-9a-f]{7,40}$ ]]; then
        echo "--expect-sha must be a commit SHA (7 to 40 hex characters)." >&2
        usage
    fi
    if [[ -z "$expect_env" ]]; then
        echo "--expect-env is required (this server's APP_ENV)." >&2
        usage
    fi

    # --- Pre-flight: nothing is changed until all of these pass ---------

    echo "==> Pre-flight checks"

    local current_branch
    current_branch=$(git rev-parse --abbrev-ref HEAD)
    if [[ "$current_branch" != "$branch" ]]; then
        preflight_fail "this checkout is on '${current_branch}', not '${branch}'. Wrong server, or the server needs 'git checkout ${branch}' first."
    fi

    local local_changes
    local_changes=$(git status --porcelain --untracked-files=no)
    if [[ -n "$local_changes" ]]; then
        preflight_fail "files tracked by git were changed on this server:
${local_changes}
Look at them with 'git diff'. Keep what's needed elsewhere, then 'git checkout -- <file>'. If they are only permission changes, run 'git config core.fileMode false'."
    fi

    local actual_env
    actual_env=$(app_env) || preflight_fail "'php artisan env' failed, so the app can't start. Its output:
${actual_env}"
    if [[ "$actual_env" != "$expect_env" ]]; then
        preflight_fail "this server's APP_ENV is '${actual_env}', not '${expect_env}'. Wrong server, or a wrong EXPECTED_APP_ENV."
    fi

    git fetch --quiet origin "$branch" || preflight_fail "'git fetch origin ${branch}' failed."
    local remote_sha old_sha
    remote_sha=$(git rev-parse "origin/${branch}")
    old_sha=$(git rev-parse HEAD)

    if [[ "$remote_sha" != "$expect_sha"* ]]; then
        echo "origin/${branch} is at ${remote_sha}, not ${expect_sha}."
        echo "Newer commit on ${branch}, skipping; the later run deploys it."
        return "$skipped"
    fi
    if ! git merge-base --is-ancestor HEAD "origin/${branch}"; then
        preflight_fail "this checkout (${old_sha}) isn't behind origin/${branch}: it has commits of its own, or ${branch} was rewritten. A fast-forward isn't possible, and this script never makes a merge commit on a server."
    fi

    echo "    branch ${branch}, APP_ENV ${actual_env}"
    echo "    now at   ${old_sha}"
    echo "    deploy   ${remote_sha}"

    if ((dry_run)); then
        echo "==> Dry run: nothing will be changed"
        echo "    $(php -v | sed -n 1p)"
        echo "    commits to deploy: $(git rev-list --count HEAD.."origin/${branch}")"
        echo "    migrations pending in the current checkout:"
        php artisan migrate:status --pending --no-interaction </dev/null || true
        echo "    migrations the new commits add:"
        git diff --name-only --diff-filter=A HEAD "origin/${branch}" -- database/migrations | sed 's/^/      /'
        echo "==> Dry run done."
        return 0
    fi

    if ((backup)); then
        local commands
        commands=$(php artisan list --raw --no-interaction </dev/null) || preflight_fail "'php artisan list' failed."
        if ! grep -q '^solva:db-backup ' <<<"$commands"; then
            preflight_fail "--backup asked for, but this checkout has no 'solva:db-backup' command yet (the first release after task #119). See 'First release' in docs/release-process.md."
        fi
        echo "==> Backing up the database"
        php artisan solva:db-backup --no-interaction </dev/null \
            || preflight_fail "the database backup failed (see above)."
    fi

    # --- Deploy: from here on, a failure prints how to recover -----------

    local maintenance_on=0

    if ((maintenance)); then
        echo "==> Maintenance mode on"
        php artisan down --retry=60 --no-interaction </dev/null \
            || preflight_fail "'php artisan down' failed."
        maintenance_on=1
    fi

    run_step "Fast-forward to ${remote_sha}" git merge --ff-only --quiet "origin/${branch}"

    local new_sha
    new_sha=$(git rev-parse HEAD)
    if [[ "$new_sha" != "$expect_sha"* ]]; then
        deploy_fail "Check the checked-out commit" "HEAD is ${new_sha}, not ${expect_sha}."
    fi

    run_step "Install PHP dependencies" composer install --no-dev --optimize-autoloader --no-interaction
    run_step "Run database migrations" php artisan migrate --force --no-interaction
    run_step "Clear compiled views" php artisan view:clear --no-interaction
    run_step "Clear the config cache" php artisan config:clear --no-interaction
    run_step "Clear the route cache" php artisan route:clear --no-interaction
    run_step "Rebuild the config cache" php artisan config:cache --no-interaction
    run_step "Rebuild the route cache" php artisan route:cache --no-interaction

    if ((maintenance_on)); then
        run_step "Maintenance mode off" php artisan up --no-interaction
        maintenance_on=0
    fi

    echo "==> Done. Deployed ${new_sha} (${branch}, APP_ENV ${actual_env})."
    echo "DEPLOYED_SHA=${new_sha}"
}

usage() {
    sed -n '8,17p' "${BASH_SOURCE[0]}" >&2
    exit 2
}

# APP_ENV as Laravel sees it ("The application environment is [production].").
app_env() {
    local output
    output=$(php artisan env --no-interaction </dev/null 2>&1) || { echo "$output"; return 1; }
    sed -n 's/.*environment is \[\(.*\)\].*/\1/p' <<<"$output"
}

# A failure before anything changed.
preflight_fail() {
    echo "" >&2
    echo "DEPLOY STOPPED before changing anything: $1" >&2
    exit 1
}

run_step() {
    local label="$1"
    shift
    echo "==> ${label}"
    if ! "$@" </dev/null; then
        deploy_fail "$label" "the command failed (see the output above)."
    fi
}

# A failure after the code started changing. Uses main's locals
# (old_sha, maintenance_on, branch) through bash's dynamic scoping.
deploy_fail() {
    {
        echo ""
        echo "!!! DEPLOY FAILED at step: $1"
        echo "!!! $2"
        echo ""
        if ((maintenance_on)); then
            echo "!!! THE SITE IS STILL IN MAINTENANCE MODE, on purpose: a half-deployed or half-migrated"
            echo "!!! site must not take traffic. It stays down until you bring it up by hand."
            echo ""
        fi
        echo "To recover by hand, on this server in $(pwd):"
        echo "  git log -1 --oneline                  # the commit now checked out"
        echo "  php artisan migrate:status            # which migrations ran"
        echo "Then either finish the deploy:"
        echo "  composer install --no-dev --optimize-autoloader"
        echo "  php artisan migrate --force"
        echo "  php artisan view:clear && php artisan config:clear && php artisan route:clear"
        echo "  php artisan config:cache && php artisan route:cache"
        echo "Or go back to the code that was live (${old_sha}):"
        echo "  git reset --hard ${old_sha} && composer install --no-dev --optimize-autoloader"
        echo "  (migrations that already ran stay; restore the backup if the data needs to go back too)"
        if ((maintenance_on)); then
            echo "Only when the site works again:"
            echo "  php artisan up"
        fi
        echo "Full runbook: docs/release-process.md, 'When a deploy fails'."
    } >&2
    exit 1
}

main "$@"
exit $?
