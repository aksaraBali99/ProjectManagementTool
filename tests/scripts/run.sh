#!/usr/bin/env bash
#
# task #119: tests for the release scripts in .github/scripts. Bash, git
# and jq (task #124; preinstalled on GitHub's ubuntu runners). CI runs it;
# locally:
#
#   bash tests/scripts/run.sh

set -euo pipefail

# The release-notes scripts need jq: fail loudly rather than skip them.
if ! command -v jq >/dev/null 2>&1; then
    echo "jq is required for these tests (winget install jqlang.jq, or apt/brew install jq)." >&2
    exit 1
fi

scripts="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.github/scripts" && pwd)"
deploy="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)/deploy.sh"
passed=0
failed=0

pass() {
    passed=$((passed + 1))
    echo "  ok   $1"
}

fail() {
    failed=$((failed + 1))
    echo "  FAIL $1"
    echo "       expected: $(printf '%q' "$2")"
    echo "       got:      $(printf '%q' "$3")"
}

expect_equal() {
    if [[ "$2" == "$3" ]]; then pass "$1"; else fail "$1" "$2" "$3"; fi
}

# expect_exit <name> <expected code> <command...>
expect_exit() {
    local name="$1" expected="$2" code=0
    shift 2
    "$@" >/dev/null 2>&1 || code=$?
    expect_equal "$name" "$expected" "$code"
}

# --- next-version.sh ----------------------------------------------------

echo "next-version.sh"
next() { printf '%s' "$1" | bash "$scripts/next-version.sh" "$2"; }

expect_equal "no tags gives .1" "v2026.11.1" "$(next "" 2026.11)"
expect_equal "v2026.11.0 and v2026.11.3 give .4" "v2026.11.4" "$(next $'v2026.11.0\nv2026.11.3\n' 2026.11)"
expect_equal "out of order, numbers not text (.10 after .9)" "v2026.11.11" "$(next $'v2026.11.10\nv2026.11.9\n' 2026.11)"
expect_equal "only last month's tag gives .1" "v2026.11.1" "$(next $'v2026.10.7\n' 2026.11)"
expect_equal "month rollover starts at 1" "v2026.12.1" "$(next $'v2026.11.5\n' 2026.12)"
expect_equal "tags not matching vYYYY.MM.N are ignored" "v2026.11.3" \
    "$(next $'v2026.11.2\nv2026.11.9-rc\nrelease-2026.11.8\nv2026.11\nv2026.11.x\nxv2026.11.7\n' 2026.11)"
expect_exit "a bad year-month is refused" 2 bash "$scripts/next-version.sh" 2026-11

# --- release-tasks.sh ---------------------------------------------------

echo "release-tasks.sh"
repo="$(mktemp -d)"
sandbox="$(mktemp -d)"
trap 'rm -rf "$repo" "$sandbox"' EXIT
(
    cd "$repo"
    git init --quiet
    git config user.name "Test"
    git config user.email "test@example.com"
    git config commit.gpgsign false
    git commit --quiet --allow-empty -m "Enhancement: before the tag (task #1)"
    git tag v2026.11.0
    git commit --quiet --allow-empty -m "Enhancement: search (task #118, phase 4/4) (#148)"
    git commit --quiet --allow-empty -m "Bugfix: a fix (task #7)"
    git commit --quiet --allow-empty -m "Chore: no task number here"
    git commit --quiet --allow-empty -m "Enhancement: more of it (Task #118)"
    git commit --quiet --allow-empty -m "Merge pull request #150 from org/main"
    git commit --quiet --allow-empty -m "Bugfix: two tasks (task #30, task #9)"
)
head_sha="$(git -C "$repo" rev-parse HEAD)"
tasks() { (cd "$repo" && bash "$scripts/release-tasks.sh" "$1" "$2"); }

expect_equal "since the previous tag: unique, sorted by number, untasked subjects skipped" \
    $'- Task #7\n- Task #9\n- Task #30\n- Task #118' "$(tasks v2026.11.0 "$head_sha")"
expect_equal "no previous tag: every commit" \
    $'- Task #1\n- Task #7\n- Task #9\n- Task #30\n- Task #118' "$(tasks "" "$head_sha")"
expect_equal "nothing new" "- None found in the commit subjects" "$(tasks "$head_sha" "$head_sha")"

# --- release-check.sh ---------------------------------------------------

echo "release-check.sh"
ticked=$'## Checklist\n- [x] Tested on DEV\n- [X] Migrations reviewed\n'
unticked=$'## Checklist\n- [x] Tested on DEV\n- [ ] Migrations reviewed\n'
check() { PR_BODY="$2" bash "$scripts/release-check.sh" "$1"; }

expect_exit "main with every box ticked passes" 0 check main "$ticked"
expect_exit "main with one unticked box fails" 1 check main "$unticked"
expect_exit "an unticked box written with * fails" 1 check main $'- [x] one\n  * [ ] two\n'
expect_exit "Windows line endings don't hide an unticked box" 1 check main $'- [x] one\r\n- [ ] two\r\n'
expect_exit "main without the template (no boxes) fails" 1 check main "Just a description"
expect_exit "Hotfix/12-x passes the source check" 0 check Hotfix/12-x "$unticked"
expect_exit "feature/x fails" 1 check feature/x "$ticked"
expect_exit "hotfix/ in lower case fails" 1 check hotfix/12-x "$ticked"
expect_exit "Enhancement/119-x fails" 1 check Enhancement/119-x "$ticked"
expect_exit "body text is never run" 0 check main $'- [x] $(touch /tmp/release-check-injected)\n'
if [[ -e /tmp/release-check-injected ]]; then
    fail "body text is never run (file created)" "no file" "file"
    rm -f /tmp/release-check-injected
fi

# --- release-notes-check.sh, release-version.sh, release-notes-body.sh --

echo "release-notes-check.sh"
notes_dir="$sandbox/notes"
mkdir -p "$notes_dir"

# notes_file <name> <json>: writes a notes file, prints its path.
notes_file() {
    printf '%s\n' "$2" >"$notes_dir/$1.json"
    echo "$notes_dir/$1.json"
}

entry='{"version": "v2026.11.1", "date": "2026-11-10", "summary": "The first release.", "tasks": [
    {"number": 118, "type": "Enhancement", "title": "Company-coded task numbers", "before": "No codes.", "after": "Every task has a code.", "prs": [144, 145]},
    {"number": 121, "type": "Bugfix", "title": "Keep the back link", "before": "Lost.", "after": "Kept.", "pr": 151},
    {"number": 119, "type": "Chore", "title": "Release process"}]}'
good=$(notes_file good "{\"releases\": [$entry]}")
printf 'v2026.11.0\n' >"$notes_dir/tags"
: >"$notes_dir/no-tags"
printf '118\n121\n119\n' >"$notes_dir/tasks"

# notes_check <notes> <next> <tags> <tasks>: the exit code in $check_code, the output in $check_output.
notes_check() {
    check_code=0
    check_output=$(bash "$scripts/release-notes-check.sh" "$@" 2>&1) || check_code=$?
}
contains() { [[ "$check_output" == *"$1"* ]] && echo yes || echo no; }

notes_check "$good" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "a matching entry passes" "0" "$check_code"
expect_equal "and says so" "yes" "$(contains 'OK: the newest entry is v2026.11.1')"
expect_equal "and warns about nothing" "no" "$(contains '::warning::')"

notes_check "$good" v2026.11.2 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "a version other than the next one fails" "1" "$check_code"
expect_equal "naming both versions" "yes" "$(contains 'the newest entry is v2026.11.1, but this release will be v2026.11.2')"

printf 'v2026.11.0\nv2026.11.1\n' >"$notes_dir/tags-taken"
notes_check "$good" v2026.11.1 "$notes_dir/tags-taken" "$notes_dir/tasks"
expect_equal "an existing tag fails" "1" "$check_code"
expect_equal "and says it's a tag" "yes" "$(contains 'v2026.11.1 is already a tag')"

printf '118\n121\n119\n125\n130\n' >"$notes_dir/tasks-more"
notes_check "$good" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks-more"
expect_equal "a task missing from the entry fails" "1" "$check_code"
expect_equal "listing the missing ones" "yes" "$(contains "doesn't list these tasks that are in the release: #125 #130")"

printf '118\n' >"$notes_dir/tasks-fewer"
notes_check "$good" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks-fewer"
expect_equal "extra tasks in the entry only warn" "0" "$check_code"
expect_equal "naming them" "yes" "$(contains '::warning::Release notes: v2026.11.1 lists tasks no commit in the release mentions: #119 #121')"

notes_check "$good" v2026.11.1 "$notes_dir/no-tags" "$notes_dir/tasks"
expect_equal "with no tags at all (the first release), the entry passes" "0" "$check_code"
expect_equal "and next-version.sh gives v2026.11.1 then" "v2026.11.1" "$(bash "$scripts/next-version.sh" 2026.11 <"$notes_dir/no-tags")"

notes_check "$(notes_file december "{\"releases\": [${entry/2026-11-10/2026-12-01}]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "a planned date in another month only warns" "0" "$check_code"
expect_equal "saying the deploy may tag another version" "yes" "$(contains '::warning::Release notes: v2026.11.1 is planned for 2026-12-01, another month')"

notes_check "$(notes_file empty '{"releases": []}')" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "no entry at all fails" "1" "$check_code"
expect_equal "asking for one" "yes" "$(contains "there's no entry for v2026.11.1")"

notes_check "$(notes_file broken '{"releases": [')" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "invalid JSON fails" "1" "$check_code"

notes_check "$(notes_file no-before "{\"releases\": [${entry/\"before\": \"Lost.\", /}]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "a Bugfix without before fails" "1" "$check_code"
expect_equal "naming the task" "yes" "$(contains 'v2026.11.1: task #121 needs "before"')"

older='{"version": "v2026.10.3", "date": "2026-10-20", "summary": "Older.", "tasks": [{"number": 90, "type": "Chore", "title": "Tidy"}]}'
notes_check "$(notes_file out-of-order "{\"releases\": [$older, $entry]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "entries out of order fail" "1" "$check_code"
expect_equal "naming both" "yes" "$(contains 'newest first, but v2026.11.1 comes after v2026.10.3')"

notes_check "$(notes_file in-order "{\"releases\": [$entry, $older]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "entries in order pass" "0" "$check_code"

notes_check "$(notes_file duplicate "{\"releases\": [$entry, $entry]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "a duplicate version fails" "1" "$check_code"
expect_equal "naming it" "yes" "$(contains 'v2026.11.1 appears more than once')"

notes_check "$(notes_file duplicate-task "{\"releases\": [${entry/\"Release process\"\}/\"Release process\"\}, {\"number\": 118, \"type\": \"Chore\", \"title\": \"Again\"\}}]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "a task listed twice in one entry fails" "1" "$check_code"
expect_equal "naming it" "yes" "$(contains 'task #118 is listed more than once')"

echo "release-version.sh"
# pick <notes> <tags-file> <previous>: the chosen tag in $picked, any warning in $pick_warning.
pick() {
    local warnings="$sandbox/pick-warnings"
    picked=$(bash "$scripts/release-version.sh" "$1" 2026.11 "$3" <"$2" 2>"$warnings")
    pick_warning=$(cat "$warnings")
}

pick "$good" "$notes_dir/tags" v2026.11.0
expect_equal "the entry's version is used" "v2026.11.1" "$picked"
expect_equal "without a warning" "" "$pick_warning"

pick "$good" "$notes_dir/no-tags" ""
expect_equal "with no previous tag (the first release), the entry's version is used" "v2026.11.1" "$picked"
expect_equal "without a warning either" "" "$pick_warning"

pick "$good" "$notes_dir/tags-taken" v2026.11.1
expect_equal "a version that's already a tag falls back to next-version.sh" "v2026.11.2" "$picked"
expect_equal "with a warning saying why" "yes" "$([[ "$pick_warning" == "::warning::"*"v2026.11.1 is already a tag"* ]] && echo yes || echo no)"

printf 'v2026.11.0\nv2026.11.3\n' >"$notes_dir/tags-ahead"
pick "$good" "$notes_dir/tags-ahead" v2026.11.3
expect_equal "a version not higher than the previous tag falls back" "v2026.11.4" "$picked"
expect_equal "with a warning" "yes" "$([[ "$pick_warning" == *"isn't higher than the previous tag v2026.11.3"* ]] && echo yes || echo no)"

pick "$(notes_file malformed "{\"releases\": [${entry/v2026.11.1/2026.11.1}]}")" "$notes_dir/tags" v2026.11.0
expect_equal "a malformed version falls back" "v2026.11.1" "$picked"
expect_equal "with a warning" "yes" "$([[ "$pick_warning" == *"isn't vYYYY.MM.N"* ]] && echo yes || echo no)"

pick "$notes_dir/broken.json" "$notes_dir/tags" v2026.11.0
expect_equal "an invalid file falls back" "v2026.11.1" "$picked"
expect_equal "with a warning" "yes" "$([[ "$pick_warning" == *"isn't valid JSON"* ]] && echo yes || echo no)"

pick "$notes_dir/empty.json" "$notes_dir/tags" v2026.11.0
expect_equal "an empty file falls back" "v2026.11.1" "$picked"
expect_equal "with a warning" "yes" "$([[ "$pick_warning" == *"has no entry"* ]] && echo yes || echo no)"

echo "release-notes-body.sh"
body=$(bash "$scripts/release-notes-body.sh" "$good" v2026.11.1)
expect_equal "the entry's tasks, grouped by type, with what changes" \
    $'## Release notes\n\nThe first release.\n\n### Enhancement\n\n- **task #118** Company-coded task numbers: Every task has a code.\n\n### Bugfix\n\n- **task #121** Keep the back link: Kept.\n\n### Chore\n\n- **task #119** Release process' \
    "$body"
expect_equal "nothing for a version with no entry" "" "$(bash "$scripts/release-notes-body.sh" "$good" v2026.11.9)"

# --- check-deploy-config.sh ---------------------------------------------

echo "check-deploy-config.sh"
# config <APP_PATH>: runs the check with every other value set.
config() {
    SSH_HOST=h SSH_PORT=65002 SSH_USER=u APP_URL=https://x EXPECTED_APP_ENV=production \
        SSH_KEY=k SSH_KNOWN_HOSTS=kh APP_PATH="$1" bash "$scripts/check-deploy-config.sh"
}

# A literal ~, as someone might type it into the variable.
# shellcheck disable=SC2088 # the ~ must stay unexpanded
tilde_path='~/domains/x/solava'
expect_exit "a full path passes" 0 config /home/u1/domains/x/solava
expect_exit "a ~ path fails" 1 config "$tilde_path"
expect_exit "a relative path fails" 1 config domains/x
expect_equal "and says why" "yes" \
    "$([[ "$(config "$tilde_path")" == *"APP_PATH must be the full path on the server"* ]] && echo yes || echo no)"
expect_exit "an empty APP_PATH fails as missing" 1 config ""
expect_equal "and is reported as missing" "yes" \
    "$([[ "$(config "")" == *"Missing config for this environment: APP_PATH."* ]] && echo yes || echo no)"
expect_equal "a missing secret is reported" "yes" \
    "$([[ "$(SSH_HOST=h SSH_PORT=1 SSH_USER=u APP_PATH=/a APP_URL=x EXPECTED_APP_ENV=p SSH_KNOWN_HOSTS=kh bash "$scripts/check-deploy-config.sh")" == *"Missing config for this environment: SSH_KEY."* ]] && echo yes || echo no)"

# --- deploy.sh ----------------------------------------------------------
#
# A throwaway origin and "server" checkout, with stub php and composer
# that log every call to $DEPLOY_TEST_LOG instead of running anything.

echo "deploy.sh"
mkdir -p "$sandbox/bin"
cat >"$sandbox/bin/php" <<'STUB'
#!/usr/bin/env bash
echo "php $*" >>"$DEPLOY_TEST_LOG"
case "$*" in
    "artisan env"*) echo "   INFO  The application environment is [staging]." ;;
    "artisan list"*) echo "solva:db-backup              Back up the database" ;;
    "artisan solva:db-backup"*) if [[ -n "${FAIL_BACKUP:-}" ]]; then exit 1; fi ;;
esac
exit 0
STUB
cat >"$sandbox/bin/composer" <<'STUB'
#!/usr/bin/env bash
echo "composer $*" >>"$DEPLOY_TEST_LOG"
if [[ -n "${FAIL_COMPOSER:-}" ]]; then exit 1; fi
STUB
chmod +x "$sandbox/bin/php" "$sandbox/bin/composer"

(
    git init --quiet --bare "$sandbox/origin.git"
    git clone --quiet "$sandbox/origin.git" "$sandbox/work" 2>/dev/null
    cd "$sandbox/work"
    git config user.name "Test"
    git config user.email "test@example.com"
    git config commit.gpgsign false
    git checkout --quiet -b main
    git commit --quiet --allow-empty -m "first"
    git push --quiet origin main
    git clone --quiet --branch main "$sandbox/origin.git" "$sandbox/server"
    git commit --quiet --allow-empty -m "second"
    git push --quiet origin main
    git checkout --quiet -b elsewhere HEAD~1
    git commit --quiet --allow-empty -m "not on main"
)
first="$(git -C "$sandbox/work" rev-parse main~1)"
second="$(git -C "$sandbox/work" rev-parse main)"
not_on_main="$(git -C "$sandbox/work" rev-parse elsewhere)"
export DEPLOY_TEST_LOG="$sandbox/calls.log"

# run_deploy <args...>: runs deploy.sh on the "server" from a fresh log;
# the exit code is in $deploy_code and the output in $deploy_output.
run_deploy() {
    : >"$DEPLOY_TEST_LOG"
    git -C "$sandbox/server" reset --quiet --hard "$first"
    deploy_code=0
    deploy_output=$(cd "$sandbox/server" && PATH="$sandbox/bin:$PATH" bash "$deploy" "$@" 2>&1) || deploy_code=$?
}

# line_of <pattern>: the log line number of the first call matching it.
line_of() { grep -n -m1 -e "$1" "$DEPLOY_TEST_LOG" | cut -d: -f1; }

run_deploy --branch main --expect-sha "$second" --expect-env staging --maintenance --backup
expect_equal "a full deploy succeeds" "0" "$deploy_code"
expect_equal "it deploys the expected commit" "$second" "$(git -C "$sandbox/server" rev-parse HEAD)"
down=$(line_of "artisan down"); backup=$(line_of "artisan solva:db-backup")
migrate=$(line_of "artisan migrate"); up=$(line_of "artisan up")
expect_equal "the backup runs after 'down'" "yes" "$( ((down < backup)) && echo yes || echo no)"
expect_equal "the backup runs before the migrations" "yes" "$( ((backup < migrate)) && echo yes || echo no)"
expect_equal "'up' runs last" "yes" "$( ((migrate < up)) && echo yes || echo no)"

FAIL_BACKUP=1 run_deploy --branch main --expect-sha "$second" --expect-env staging --maintenance --backup
expect_equal "a failed backup fails the deploy" "1" "$deploy_code"
expect_equal "a failed backup pulls nothing" "$first" "$(git -C "$sandbox/server" rev-parse HEAD)"
expect_equal "a failed backup migrates nothing" "" "$(line_of "artisan migrate")"
expect_equal "a failed backup runs no composer" "" "$(line_of "composer")"
expect_equal "a failed backup brings the site back up, after the backup" "yes" \
    "$(up=$(line_of "artisan up"); backup=$(line_of "artisan solva:db-backup"); [[ -n "$up" ]] && ((backup < up)) && echo yes || echo no)"
expect_equal "and says so" "yes" "$([[ "$deploy_output" == *"Nothing was pulled or migrated, and the site is up"* ]] && echo yes || echo no)"

FAIL_COMPOSER=1 run_deploy --branch main --expect-sha "$second" --expect-env staging --maintenance
expect_equal "a failure after the pull fails the deploy" "1" "$deploy_code"
expect_equal "it leaves the site down (no 'up')" "" "$(line_of "artisan up")"
expect_equal "its go-back steps say not to re-run the deploy" "yes" \
    "$([[ "$deploy_output" == *"don't re-run this deploy"*"a revert PR into main"* ]] && echo yes || echo no)"

run_deploy --branch main --expect-sha "$first" --expect-env staging
expect_equal "an older commit than the branch tip is skipped (78)" "78" "$deploy_code"
expect_equal "a skip changes nothing" "$first" "$(git -C "$sandbox/server" rev-parse HEAD)"

run_deploy --branch main --expect-sha "$not_on_main" --expect-env staging
expect_equal "a commit not on the branch fails instead of skipping" "1" "$deploy_code"
expect_equal "and says why" "yes" "$([[ "$deploy_output" == *"doesn't contain ${not_on_main}"* ]] && echo yes || echo no)"

run_deploy --branch main --expect-sha "$second" --expect-env production
expect_equal "the wrong APP_ENV stops it" "1" "$deploy_code"
expect_equal "before anything is called but the env check" "php artisan env --no-interaction" "$(cat "$DEPLOY_TEST_LOG")"

echo ""
echo "${passed} passed, ${failed} failed"
((failed == 0))
