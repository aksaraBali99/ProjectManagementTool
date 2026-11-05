#!/usr/bin/env bash
#
# task #119: tests for the release scripts in .github/scripts. Plain bash
# and git, nothing else to install. CI runs it; locally:
#
#   bash tests/scripts/run.sh

set -euo pipefail

scripts="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.github/scripts" && pwd)"
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
trap 'rm -rf "$repo"' EXIT
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

echo ""
echo "${passed} passed, ${failed} failed"
((failed == 0))
