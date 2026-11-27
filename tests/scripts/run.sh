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

# has <text> <part>: yes if the text contains the part.
has() { [[ "$1" == *"$2"* ]] && echo yes || echo no; }

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

# SOL-0021: task codes, in a second repo so the number-only cases above
# stay as they were.
coded="$sandbox/coded-repo"
mkdir -p "$coded"
(
    cd "$coded"
    git init --quiet
    git config user.name "Test"
    git config user.email "test@example.com"
    git config commit.gpgsign false
    git commit --quiet --allow-empty -m "Enhancement: before the tag (SOL-0001)"
    git tag v2026.12.0
    git commit --quiet --allow-empty -m "Enhancement: lanes (SOL-0025)"
    git commit --quiet --allow-empty -m "Enhancement: phase two (SOL-0025, phase 2/2) (#160)"
    git commit --quiet --allow-empty -m "Chore: gate (sol-0023)"
    git tag codes-only
    # A parenthesised item counts only when it is exactly a code: not
    # "ISO-8601 dates". (A bare "(ISO-8601)" has a code's shape, so it
    # would count: codes aren't hard-coded per company.)
    git commit --quiet --allow-empty -m "Chore: dates as (ISO-8601 dates)"
    git commit --quiet --allow-empty -m "Chore: two tasks (SOL-0021, AVA-0003)"
    git commit --quiet --allow-empty -m "Chore: a SOL-0099 mention outside parentheses"
    git commit --quiet --allow-empty -m "Merge pull request #161 from org/Bugfix/SOL-0040-login"
    git commit --quiet --allow-empty -m "Merge pull request #150 from org/main"
    git commit --quiet --allow-empty -m "Chore: both (task #119) (SOL-0021)"
    git commit --quiet --allow-empty -m "Chore: code last in the group (phase 1/3, SOL-0009)"
    git commit --quiet --allow-empty -m "Chore: not codes (UTF-8) (ABCD-0001) (SO-0021) (SOL-021)"
    git commit --quiet --allow-empty -m "Chore: a long number (SOL-12345)"
    git commit --quiet --allow-empty -m "Bugfix: an old one (task #7)"
)
coded_sha="$(git -C "$coded" rev-parse HEAD)"
coded_tasks() { (cd "$coded" && bash "$scripts/release-tasks.sh" "$@"); }

expect_equal "codes in parentheses, merge branches and old numbers: codes first (prefix, then number), then old numbers" \
    $'- AVA-0003\n- SOL-0009\n- SOL-0021\n- SOL-0023\n- SOL-0025\n- SOL-0040\n- SOL-12345\n- Task #7\n- Task #119' \
    "$(coded_tasks v2026.12.0 "$coded_sha")"
expect_equal "--ids: one id per line" \
    $'AVA-0003\nSOL-0009\nSOL-0021\nSOL-0023\nSOL-0025\nSOL-0040\nSOL-12345\n#7\n#119' \
    "$(coded_tasks --ids v2026.12.0 "$coded_sha")"
expect_equal "only codes (lowercase uppercased, a code in several commits once)" \
    $'- SOL-0023\n- SOL-0025' "$(coded_tasks v2026.12.0 codes-only)"
expect_equal "no previous tag: every commit" \
    $'AVA-0003\nSOL-0001\nSOL-0009\nSOL-0021\nSOL-0023\nSOL-0025\nSOL-0040\nSOL-12345\n#7\n#119' \
    "$(coded_tasks --ids "" "$coded_sha")"
expect_equal "--ids: old numbers as #N" $'#7\n#9\n#30\n#118' "$(cd "$repo" && bash "$scripts/release-tasks.sh" --ids v2026.11.0 "$head_sha")"
expect_equal "--ids with nothing found prints nothing" "" "$(coded_tasks --ids "$coded_sha" "$coded_sha")"
expect_exit "a missing sha is refused" 2 coded_tasks v2026.12.0
expect_exit "an unknown previous tag fails (release-check then stops with an error)" 128 coded_tasks --ids v9999.99.9 "$coded_sha"
expect_exit "an empty sha is refused" 2 coded_tasks v2026.12.0 ""
expect_exit "a missing sha is refused with --ids" 2 coded_tasks --ids v2026.12.0
expect_exit "an empty sha is refused with --ids" 2 coded_tasks --ids v2026.12.0 ""

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
expect_exit "Hotfix/SOL-0030-x passes the source check" 0 check Hotfix/SOL-0030-x "$unticked"
expect_exit "Enhancement/SOL-0021-x fails" 1 check Enhancement/SOL-0021-x "$ticked"
expect_equal "the error names the code format" "yes" \
    "$([[ "$(check feature/x "$ticked" 2>&1 || true)" == *"Hotfix/<code>-<slug> branch (for example Hotfix/SOL-0030-fix-login)"* ]] && echo yes || echo no)"
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
expect_equal "and that the deploy still tags the entry's version" "yes" "$(contains 'The deploy still tags it v2026.11.1')"

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
expect_equal "naming it" "yes" "$(contains 'v2026.11.1 lists task #118 more than once')"

# The same rules as App\Support\ReleaseNotes (ReleaseNotesPageTest has the
# same cases): what the page rejects, this rejects.
# rule_case <name> <json for the releases list> <expected message part>
rule_case() {
    notes_check "$(notes_file "rule-$RANDOM" "{\"releases\": $2}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
    expect_equal "$1 fails" "1" "$check_code"
    expect_equal "$1: says why" "yes" "$(contains "$3")"
}
chore() { echo "[{\"version\": \"v2026.11.1\", \"date\": \"2026-11-10\", \"summary\": \"S.\", \"tasks\": [$1]}]"; }

rule_case "a Chore with an empty before" "$(chore '{"number": 119, "type": "Chore", "title": "T", "before": ""}')" 'task #119: "before" must be text'
rule_case "a Chore with a non-text after" "$(chore '{"number": 119, "type": "Chore", "title": "T", "after": 5}')" 'task #119: "after" must be text'
rule_case "a task number written 5.0" "$(chore '{"number": 5.0, "type": "Chore", "title": "T"}')" 'a task needs a "number"'
rule_case "PR number 0" "$(chore '{"number": 119, "type": "Chore", "title": "T", "pr": 0}')" 'a PR number must be a whole number'
rule_case "a PR number as text" "$(chore '{"number": 119, "type": "Chore", "title": "T", "prs": ["150"]}')" 'a PR number must be a whole number'
rule_case "prs that isn't a list" "$(chore '{"number": 119, "type": "Chore", "title": "T", "prs": 150}')" '"prs" must be a list'
rule_case "a month 13" '[{"version": "v2026.11.1", "date": "2026-13-45", "summary": "S.", "tasks": [{"number": 1, "type": "Chore", "title": "T"}]}]' 'needs a "date"'
rule_case "29 February in a non-leap year" '[{"version": "v2026.11.1", "date": "2026-02-29", "summary": "S.", "tasks": [{"number": 1, "type": "Chore", "title": "T"}]}]' 'needs a "date"'
rule_case "a release that isn't an object" '["oops"]' 'release #1 must be an object'
rule_case "a task that isn't an object" "$(chore '"oops"')" 'every task must be an object'

notes_check "$(notes_file leap "{\"releases\": [${entry/2026-11-10/2028-02-29}]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/tasks"
expect_equal "29 February in a leap year is a real date" "no" "$(contains 'needs a "date"')"

# SOL-0021: tasks named by code.
code_entry='{"version": "v2026.11.1", "date": "2026-11-10", "summary": "Codes.", "tasks": [
    {"code": "SOL-0025", "type": "Enhancement", "title": "Kanban by project", "before": "One set of columns.", "after": "One lane per project.", "prs": [159, 160]},
    {"code": "SOL-0023", "type": "Chore", "title": "SSH deploy gate", "pr": 158},
    {"code": "AVA-0003", "type": "Chore", "title": "Another company"}]}'
coded_notes=$(notes_file coded "{\"releases\": [$code_entry]}")
printf 'SOL-0025\nSOL-0023\nAVA-0003\n' >"$notes_dir/code-tasks"

notes_check "$coded_notes" v2026.11.1 "$notes_dir/tags" "$notes_dir/code-tasks"
expect_equal "a code-only entry with a matching tasks file passes" "0" "$check_code"
expect_equal "every task in it" "yes" "$(contains 'OK: every task in the release is in v2026.11.1')"

printf 'SOL-0025\nSOL-0023\nAVA-0003\nSOL-0030\n' >"$notes_dir/code-tasks-more"
notes_check "$coded_notes" v2026.11.1 "$notes_dir/tags" "$notes_dir/code-tasks-more"
expect_equal "a code missing from the entry fails" "1" "$check_code"
expect_equal "naming it" "yes" "$(contains "doesn't list these tasks that are in the release: SOL-0030.")"

printf 'SOL-0030\n#125\nAVA-0009\nSOL-0025\nSOL-0023\nAVA-0003\n' >"$notes_dir/code-tasks-mixed-missing"
notes_check "$coded_notes" v2026.11.1 "$notes_dir/tags" "$notes_dir/code-tasks-mixed-missing"
expect_equal "missing ones: codes first (by prefix, then number), then numbers" "yes" \
    "$(contains "doesn't list these tasks that are in the release: AVA-0009 SOL-0030 #125.")"

printf 'SOL-0025\n' >"$notes_dir/code-tasks-fewer"
notes_check "$coded_notes" v2026.11.1 "$notes_dir/tags" "$notes_dir/code-tasks-fewer"
expect_equal "extra codes in the entry only warn" "0" "$check_code"
expect_equal "naming them, sorted" "yes" "$(contains 'lists tasks no commit in the release mentions: AVA-0003 SOL-0023')"

mixed_entry='{"version": "v2026.11.1", "date": "2026-11-10", "summary": "Both.", "tasks": [
    {"code": "SOL-0021", "type": "Chore", "title": "Codes"},
    {"number": 118, "type": "Chore", "title": "Old one"},
    {"number": 121, "type": "Chore", "title": "Another old one"}]}'
printf 'SOL-0021\r\n#118\r\n121\r\nnot an id\r\n' >"$notes_dir/mixed-tasks"
notes_check "$(notes_file mixed "{\"releases\": [$mixed_entry]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/mixed-tasks"
expect_equal "a mixed tasks file (a code, #N, a bare N, \\r line ends, a stray line) compares correctly" "0" "$check_code"
expect_equal "with nothing extra" "no" "$(contains '::warning::Release notes: v2026.11.1 lists tasks')"

same_digits='{"version": "v2026.11.1", "date": "2026-11-10", "summary": "S.", "tasks": [
    {"code": "ABC-0118", "type": "Chore", "title": "Coded"},
    {"number": 118, "type": "Chore", "title": "Numbered"}]}'
printf 'ABC-0118\n#118\n' >"$notes_dir/same-digits-tasks"
notes_check "$(notes_file same-digits "{\"releases\": [$same_digits]}")" v2026.11.1 "$notes_dir/tags" "$notes_dir/same-digits-tasks"
expect_equal "a code and a number with the same digits don't clash" "0" "$check_code"

needs_code='needs a task "code" like SOL-0021 (or an old "number")'
rule_case "a task with neither code nor number" "$(chore '{"type": "Chore", "title": "T"}')" "v2026.11.1, task entry #1 $needs_code"
rule_case "a task with both code and number" "$(chore '{"code": "SOL-0021", "number": 21, "type": "Chore", "title": "T"}')" 'v2026.11.1, task entry #1 has both "code" and "number"; use one'
rule_case "both, with a bad code" "$(chore '{"code": "sol-21", "number": 21, "type": "Chore", "title": "T"}')" 'has both "code" and "number"; use one'
expect_equal "both, with a bad code: only the \"has both\" message" "no" "$(contains "$needs_code")"
rule_case "a lowercase code" "$(chore '{"code": "sol-0021", "type": "Chore", "title": "T"}')" "$needs_code"
rule_case "a code with too few digits" "$(chore '{"code": "SOL-21", "type": "Chore", "title": "T"}')" "$needs_code"
rule_case "a code with four letters" "$(chore '{"code": "SOLA-0021", "type": "Chore", "title": "T"}')" "$needs_code"
rule_case "a code with a line break after it" "$(chore '{"code": "SOL-0021\n", "type": "Chore", "title": "T"}')" "$needs_code"
rule_case "a code with a line break before it" "$(chore '{"code": "x\nSOL-0021", "type": "Chore", "title": "T"}')" "$needs_code"
rule_case "a code that isn't text" "$(chore '{"code": 21, "type": "Chore", "title": "T"}')" "$needs_code"
rule_case "the same code twice in one entry" "$(chore '{"code": "SOL-0021", "type": "Chore", "title": "T"}, {"code": "SOL-0021", "type": "Chore", "title": "U"}')" 'v2026.11.1 lists SOL-0021 more than once'
rule_case "a code task missing before" "$(chore '{"code": "SOL-0025", "type": "Enhancement", "title": "T", "after": "A"}')" 'v2026.11.1: SOL-0025 needs "before" (text)'

echo "release-notes-entry.sh"
# entry_of <notes> <tags-file>: the printed version in $entry_out, any warning in $entry_warning.
entry_of() {
    local warnings="$sandbox/entry-warnings"
    entry_out=$(bash "$scripts/release-notes-entry.sh" "$1" <"$2" 2>"$warnings")
    entry_warning=$(cat "$warnings")
}

entry_of "$good" "$notes_dir/tags"
expect_equal "the newest entry, not yet a tag, is this release's" "v2026.11.1" "$entry_out"
expect_equal "without a warning" "" "$entry_warning"

entry_of "$good" "$notes_dir/tags-taken"
expect_equal "an entry that's already a tag (stale) gives no notes" "" "$entry_out"
expect_equal "with a warning saying why" "yes" "$([[ "$entry_warning" == *"v2026.11.1, is already a tag"* ]] && echo yes || echo no)"

entry_of "$notes_dir/broken.json" "$notes_dir/tags"
expect_equal "an invalid file gives no notes" "" "$entry_out"
expect_equal "with a warning" "yes" "$([[ "$entry_warning" == *"no usable entry"* ]] && echo yes || echo no)"

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
expect_equal "a code task prints its code; an old one task #N" \
    $'## Release notes\n\nBoth.\n\n### Chore\n\n- **SOL-0021** Codes\n- **task #118** Old one\n- **task #121** Another old one' \
    "$(bash "$scripts/release-notes-body.sh" "$notes_dir/mixed.json" v2026.11.1)"
expect_equal "with what changes for people" "yes" \
    "$([[ "$(bash "$scripts/release-notes-body.sh" "$coded_notes" v2026.11.1)" == *"- **SOL-0025** Kanban by project: One lane per project."* ]] && echo yes || echo no)"
unreadable=$(notes_file unreadable '{"releases": [{"version": "v2026.11.1", "summary": "S.", "tasks": "oops"}]}')
body_warning="$sandbox/body-warning"
expect_equal "an entry that can't be read gives nothing, not a partial section" "" "$(bash "$scripts/release-notes-body.sh" "$unreadable" v2026.11.1 2>"$body_warning")"
expect_equal "with a warning" "yes" "$([[ "$(cat "$body_warning")" == *"couldn't be read"* ]] && echo yes || echo no)"

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

# SOL-0023: gate_config <NAME=value...>: the check with every non-key value set.
gate_config() {
    env SSH_HOST=h SSH_PORT=65002 SSH_USER=u APP_PATH=/a APP_URL=https://x EXPECTED_APP_ENV=p SSH_KNOWN_HOSTS=kh \
        "$@" bash "$scripts/check-deploy-config.sh"
}
expect_exit "SSH_GATE on with SSH_GATE_KEY and no SSH_KEY passes" 0 gate_config SSH_GATE=true SSH_GATE_KEY=gk
expect_exit "SSH_GATE on without SSH_GATE_KEY fails, even with SSH_KEY" 1 gate_config SSH_GATE=true SSH_KEY=k
expect_equal "and says SSH_GATE_KEY isn't set" "yes" \
    "$([[ "$(gate_config SSH_GATE=true SSH_KEY=k)" == *"SSH_GATE is on, but SSH_GATE_KEY isn't set"* ]] && echo yes || echo no)"
expect_exit "SSH_GATE off still needs SSH_KEY" 1 gate_config SSH_GATE=false SSH_GATE_KEY=gk
expect_exit "SSH_GATE off with SSH_KEY passes" 0 gate_config SSH_GATE=false SSH_KEY=k

# --- deploy-remote-command.sh -------------------------------------------

echo "deploy-remote-command.sh"
fixed_sha=0123456789abcdef0123456789abcdef01234567
# remote_cmd <SSH_GATE> <BRANCH> <EXPECTED_APP_ENV> <APP_PATH> <DEPLOY_ARGS> <DRY_RUN>
remote_cmd() {
    env SSH_GATE="$1" BRANCH="$2" SHA="$fixed_sha" EXPECTED_APP_ENV="$3" APP_PATH="$4" DEPLOY_ARGS="$5" DRY_RUN="$6" \
        bash "$scripts/deploy-remote-command.sh"
}

# The expected lines were captured from the action's own code before
# SOL-0023 (its inline printf), not from this script. $HOME stays literal,
# for the server's shell to expand.
# shellcheck disable=SC2016 # literal $HOME, $? and $code on purpose
legacy_prod='cd /home/u1/domains/x/solava && git fetch --quiet origin production && git show 0123456789abcdef0123456789abcdef01234567:deploy.sh > $HOME/.deploy-0123456789abcdef0123456789abcdef01234567.sh && bash $HOME/.deploy-0123456789abcdef0123456789abcdef01234567.sh --branch production --expect-sha 0123456789abcdef0123456789abcdef01234567 --expect-env production --maintenance --backup ; code=$?; rm -f $HOME/.deploy-0123456789abcdef0123456789abcdef01234567.sh; exit $code'
# shellcheck disable=SC2016
legacy_dev_dry='cd /home/u1/domains/a\ b/solava && git fetch --quiet origin main && git show 0123456789abcdef0123456789abcdef01234567:deploy.sh > $HOME/.deploy-0123456789abcdef0123456789abcdef01234567.sh && bash $HOME/.deploy-0123456789abcdef0123456789abcdef01234567.sh --branch main --expect-sha 0123456789abcdef0123456789abcdef01234567 --expect-env dev --dry-run ; code=$?; rm -f $HOME/.deploy-0123456789abcdef0123456789abcdef01234567.sh; exit $code'
expect_equal "gate off, PROD: byte for byte the line from before the gate" \
    "$legacy_prod" "$(remote_cmd false production production /home/u1/domains/x/solava "--maintenance --backup" false)"
expect_equal "gate off, DEV dry run, a path with a space: byte for byte the same" \
    "$legacy_dev_dry" "$(remote_cmd "" main dev "/home/u1/domains/a b/solava" "" true)"
expect_equal "gate on: just deploy <sha>, deploy-args ignored" \
    "deploy $fixed_sha" "$(remote_cmd true production production /home/u1/x "--maintenance --backup" false)"
expect_equal "gate on, dry run: deploy <sha> --dry-run" \
    "deploy $fixed_sha --dry-run" "$(remote_cmd true main dev /home/u1/x "" true)"

# --- check-gate-ping.sh -------------------------------------------------

echo "check-gate-ping.sh"
# ping_check <environment> <code> <reply>: the exit code in $ping_code, the output in $ping_output.
ping_check() {
    ping_code=0
    ping_output=$(bash "$scripts/check-gate-ping.sh" "$@" 2>&1) || ping_code=$?
}
ping_check production 0 "ok solava-gate production"
expect_equal "the gate answering for this environment passes" "0" "$ping_code"
ping_check production 0 "ok solava-gate dev"
expect_equal "the DEV gate key in the production environment fails" "1" "$ping_code"
expect_equal "naming both" "yes" "$(has "$ping_output" "in the 'production' environment is the gate key for 'dev', not 'production'")"
ping_check dev 0 "ok solava-gate production"
expect_equal "the PROD gate key in the dev environment fails" "1" "$ping_code"
ping_check dev 2 "Usage: ping [-aAbBdDfhLnOqrRUvV64] destination"
expect_equal "a key with no gate (the shell ran ping) fails" "1" "$ping_code"
expect_equal "saying the key line probably has no gate" "yes" "$(has "$ping_output" "probably has no gate")"
ping_check dev 0 "ok solava-gate dev extra"
expect_equal "anything but the exact answer fails" "1" "$ping_code"
ping_check dev 126 ""
expect_equal "the gate refusing ping fails" "1" "$ping_code"
expect_equal "pointing at gate.log" "yes" "$(has "$ping_output" "gate.log")"
ping_check dev 255 ""
expect_equal "an SSH failure says so" "yes" "$(has "$ping_output" "SSH itself failed")"
ping_check dev 2 $'line one\nline two'
expect_equal "the reply is shown on one line" "yes" "$(has "$ping_output" "reply: 'line one line two'")"
# SOL-0029: SSH's stderr at the end of the message.
ping_check dev 255 "" "ssh: connect to host example.com port 22: Connection timed out"
expect_equal "an SSH failure shows SSH's reason" "yes" "$(has "$ping_output" "SSH said: 'ssh: connect to host example.com port 22: Connection timed out'")"
ping_check dev 255 "" $'Host key verification failed.\r\n'
expect_equal "SSH's reason is shown on one line" "yes" "$(has "$ping_output" "SSH said: 'Host key verification failed.  '")"
expect_equal "  with nothing after the error line" "1" "$(printf '%s\n' "$ping_output" | wc -l | tr -d ' ')"
ping_check dev 255 "" $'reason\n::add-mask::x %0A::warning::y'
expect_equal "SSH's stderr can't start a workflow command" "1" "$(printf '%s\n' "$ping_output" | grep -c '^::')"
expect_equal "  and its % is escaped" "yes" "$(has "$ping_output" "%250A")"
ping_check dev 255 "" "$(printf 'b%.0s' {1..400}) Connection reset by peer"
expect_equal "long stderr keeps its last 300 characters" "yes" "$(has "$ping_output" "SSH said: '...$(printf 'b%.0s' {1..275}) Connection reset by peer'")"
expect_equal "  and no more" "no" "$(has "$ping_output" "$(printf 'b%.0s' {1..276})")"
ping_check dev 255 "" "$(printf 'x%.0s' {1..300})"
expect_equal "stderr of exactly 300 characters isn't marked as cut" "yes" "$(has "$ping_output" "SSH said: '$(printf 'x%.0s' {1..300})'")"
ping_check production 0 "ok solava-gate dev" "gate: note"
expect_equal "the wrong-environment error shows stderr too" "yes" "$(has "$ping_output" "SSH said: 'gate: note'")"
ping_check dev 255 "" $' \n '
expect_equal "blank stderr adds nothing" "no" "$(has "$ping_output" "SSH said")"
ping_check dev 126 "" "gate: refused"
expect_equal "the gate refusing ping shows its stderr too" "yes" "$(has "$ping_output" "SSH said: 'gate: refused'")"
ping_check dev 0 "ok solava-gate dev" "some warning"
expect_equal "stderr doesn't fail a good answer" "0" "$ping_code"

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
    # OLD_BACKUP_NAME: a checkout from before SOL-0013 renamed the command;
    # NO_BACKUP_COMMAND: one from before task #119 added it.
    "artisan list"*)
        if [[ -n "${NO_BACKUP_COMMAND:-}" ]]; then echo "migrate                 Run the migrations"
        elif [[ -n "${OLD_BACKUP_NAME:-}" ]]; then echo "solva:db-backup              Back up the database"
        else echo "solava:db-backup              Back up the database"; fi ;;
    "artisan solava:db-backup"*|"artisan solva:db-backup"*) if [[ -n "${FAIL_BACKUP:-}" ]]; then exit 1; fi ;;
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
down=$(line_of "artisan down"); backup=$(line_of "artisan solava:db-backup")
migrate=$(line_of "artisan migrate"); up=$(line_of "artisan up")
expect_equal "the backup runs after 'down'" "yes" "$( ((down < backup)) && echo yes || echo no)"
expect_equal "the backup runs before the migrations" "yes" "$( ((backup < migrate)) && echo yes || echo no)"
expect_equal "'up' runs last" "yes" "$( ((migrate < up)) && echo yes || echo no)"

OLD_BACKUP_NAME=1 run_deploy --branch main --expect-sha "$second" --expect-env staging --maintenance --backup
expect_equal "a checkout from before the rename still backs up" "0" "$deploy_code"
expect_equal "  with its own name for the command" "yes" "$([[ -n "$(line_of "artisan solva:db-backup")" ]] && echo yes || echo no)"

NO_BACKUP_COMMAND=1 run_deploy --branch main --expect-sha "$second" --expect-env staging --maintenance --backup
expect_equal "a checkout with no backup command stops before anything" "1" "$deploy_code"
expect_equal "  and pulls nothing" "$first" "$(git -C "$sandbox/server" rev-parse HEAD)"

FAIL_BACKUP=1 run_deploy --branch main --expect-sha "$second" --expect-env staging --maintenance --backup
expect_equal "a failed backup fails the deploy" "1" "$deploy_code"
expect_equal "a failed backup pulls nothing" "$first" "$(git -C "$sandbox/server" rev-parse HEAD)"
expect_equal "a failed backup migrates nothing" "" "$(line_of "artisan migrate")"
expect_equal "a failed backup runs no composer" "" "$(line_of "composer")"
expect_equal "a failed backup brings the site back up, after the backup" "yes" \
    "$(up=$(line_of "artisan up"); backup=$(line_of "artisan solava:db-backup"); [[ -n "$up" ]] && ((backup < up)) && echo yes || echo no)"
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

# --- server/solava-gate.sh (SOL-0023) -----------------------------------
#
# A sandbox copy of the gate with its own gate.conf, pointing at a dev and
# a production "server" checkout of a throwaway origin. Each commit's
# deploy.sh records its arguments and umask, then runs the real deploy.sh
# (with the stub php and composer above, APP_ENV "staging").

echo "solava-gate.sh"
g="$sandbox/gate"
gate="$g/home/bin/gate.sh"
mkdir -p "$g/home/bin"
cp "$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)/server/solava-gate.sh" "$gate"

cat >"$g/deploy-wrapper.sh" <<'WRAPPER'
#!/usr/bin/env bash
printf '%s\n' "$*" >"$GATE_TEST_ARGS"
umask >"$GATE_TEST_UMASK"
exec bash "$GATE_TEST_REAL_DEPLOY" "$@"
WRAPPER

(
    git init --quiet --bare "$g/origin.git"
    git clone --quiet "$g/origin.git" "$g/work" 2>/dev/null
    cd "$g/work"
    git config user.name "Test"
    git config user.email "test@example.com"
    git config commit.gpgsign false
    git config core.autocrlf false
    git checkout --quiet -b main
    cp "$g/deploy-wrapper.sh" deploy.sh
    git add deploy.sh
    git commit --quiet -m "first"
    git push --quiet origin main
    git branch production
    git push --quiet origin production
    git clone --quiet --branch main "$g/origin.git" "$g/server-dev"
    git clone --quiet --branch production "$g/origin.git" "$g/server-prod"
    git commit --quiet --allow-empty -m "second"
    git push --quiet origin main
    git checkout --quiet production
    git commit --quiet --allow-empty -m "second on production"
    git push --quiet origin production
    # A commit that isn't on main, whose deploy.sh leaves a marker if run.
    git checkout --quiet -b elsewhere main~1
    # shellcheck disable=SC2016 # expanded when that deploy.sh runs
    printf '#!/usr/bin/env bash\ntouch "$GATE_TEST_MARKER"\n' >deploy.sh
    git commit --quiet -am "not on main"
    git push --quiet origin elsewhere
)
# The server has the commit's objects; the gate must still refuse it.
git -C "$g/server-dev" fetch --quiet origin elsewhere
dev1="$(git -C "$g/work" rev-parse main~1)"
dev2="$(git -C "$g/work" rev-parse main)"
prod1="$(git -C "$g/work" rev-parse production~1)"
prod2="$(git -C "$g/work" rev-parse production)"
off_branch="$(git -C "$g/work" rev-parse elsewhere)"

conf="$g/home/bin/gate.conf"
cat >"$conf" <<CONF
dev_app_path=$(printf '%q' "$g/server-dev")
dev_app_env=staging
production_app_path=$(printf '%q' "$g/server-prod")
production_app_env=staging
CONF
chmod 600 "$conf"

export GATE_TEST_ARGS="$g/args" GATE_TEST_UMASK="$g/umask" GATE_TEST_MARKER="$g/marker" GATE_TEST_REAL_DEPLOY="$deploy"
gate_log="$g/home/logs/gate.log"
dev_lock="$g/home/run/dev.lock"

# run_gate <env> <SSH_ORIGINAL_COMMAND>: the gate as sshd runs it, on
# fresh checkouts; the exit code in $gate_code, the output in $gate_output.
run_gate() {
    : >"$DEPLOY_TEST_LOG"
    rm -f "$GATE_TEST_ARGS" "$GATE_TEST_UMASK" "$GATE_TEST_MARKER"
    git -C "$g/server-dev" reset --quiet --hard "$dev1"
    git -C "$g/server-prod" reset --quiet --hard "$prod1"
    gate_code=0
    gate_output=$(cd "$g" && SSH_ORIGINAL_COMMAND="$2" SSH_CLIENT="203.0.113.7 51000 65002" \
        PATH="$sandbox/bin:$PATH" bash "$gate" "$1" 2>&1) || gate_code=$?
}
deploy_ran() { [[ -e "$GATE_TEST_ARGS" ]] && echo yes || echo no; }
last_log() { tail -n 1 "$gate_log"; }

run_gate dev ping
expect_equal "ping answers" "ok solava-gate dev" "$gate_output"
expect_equal "ping exits 0" "0" "$gate_code"
run_gate production ping
expect_equal "ping names the key's environment" "ok solava-gate production" "$gate_output"

# refused_case <name> <env> <command>
refused_case() {
    run_gate "$2" "$3"
    expect_equal "refused (126): $1" "126" "$gate_code"
    expect_equal "refused: $1: deploy.sh never ran" "no" "$(deploy_ran)"
}
refused_case "an empty command (a login shell)" dev ""
refused_case "bash" dev "bash"
refused_case "a second command after ;" dev "deploy $dev2; id"
refused_case "a second line" dev "deploy $dev2"$'\n'"id"
refused_case "a trailing newline" dev "deploy $dev2"$'\n'
refused_case "a short SHA" dev "deploy ${dev2:0:12}"
refused_case "an uppercase SHA" dev "deploy ${dev2^^}"
refused_case "two spaces" dev "deploy  $dev2"
refused_case "an extra deploy.sh flag" dev "deploy $dev2 --maintenance"
refused_case "a flag after --dry-run" dev "deploy $dev2 --dry-run --backup"
refused_case "scp" dev "scp -t ."
refused_case "the sftp subsystem" dev "/usr/libexec/openssh/sftp-server"
refused_case "rsync" dev "rsync --server -e.LsfxC . ."
refused_case "an unknown environment" staging "ping"
refused_case "a deploy for an unknown environment" staging "deploy $dev2"
expect_equal "refused says so on stderr" "yes" "$(has "$(run_gate dev bash; echo "$gate_output")" "refused")"

run_gate dev "deploy $off_branch"
expect_equal "a commit not on the branch stops before deploy.sh (65)" "65" "$gate_code"
expect_equal "and says so" "yes" "$(has "$gate_output" "isn't on origin/main. Nothing on the site changed")"
expect_equal "and its deploy.sh never ran" "no" "$([[ -e "$GATE_TEST_MARKER" ]] && echo yes || echo no)"
expect_equal "the log says stopped, with the code" "yes" "$(has "$(last_log)" "verdict=stopped exit=65")"

# A second sandbox gate whose gate.conf points at a folder that isn't there.
mkdir -p "$g/badhome/bin"
cp "$gate" "$g/badhome/bin/gate.sh"
printf 'dev_app_path=%q\n' "$g/no-such-folder" >"$g/badhome/bin/gate.conf"
chmod 600 "$g/badhome/bin/gate.conf"
bad_code=0
bad_output=$(SSH_ORIGINAL_COMMAND="deploy $dev2" bash "$g/badhome/bin/gate.sh" dev 2>&1) || bad_code=$?
expect_equal "a missing app folder stops before deploy.sh (65), not a raw error" "65" "$bad_code"
expect_equal "and names the folder" "yes" "$(has "$bad_output" "the app folder $g/no-such-folder doesn't exist")"
expect_equal "and releases the lock" "no" "$([[ -e "$g/badhome/run/dev.lock" ]] && echo yes || echo no)"

run_gate dev "deploy $dev2"
expect_equal "dev: a deploy succeeds" "0" "$gate_code"
expect_equal "dev: deploy.sh gets no extra flags" "--branch main --expect-sha $dev2 --expect-env staging" "$(cat "$GATE_TEST_ARGS")"
expect_equal "dev: it deploys the commit" "$dev2" "$(git -C "$g/server-dev" rev-parse HEAD)"
expect_equal "dev: no maintenance mode or backup" "" "$(line_of "artisan down")$(line_of "solava:db-backup")"
expect_equal "deploy.sh keeps the caller's umask (the gate never sets it)" "$(umask)" "$(cat "$GATE_TEST_UMASK")"
expect_equal "the lock is gone afterwards" "no" "$([[ -e "$dev_lock" ]] && echo yes || echo no)"
expect_equal "no temp script is left behind" "" "$(find "$g/home/run" -name 'deploy.*' 2>/dev/null)"
expect_equal "the log has the accepted deploy and its exit code" "yes" "$(has "$(last_log)" "env=dev ip=203.0.113.7 verdict=accepted exit=0 cmd=deploy $dev2")"

expect_equal "an unusual umask (0027) reaches deploy.sh unchanged" "0027" \
    "$(umask 0027 && run_gate dev "deploy $dev2" && cat "$GATE_TEST_UMASK")"

run_gate production "deploy $prod2"
expect_equal "production: a deploy succeeds" "0" "$gate_code"
expect_equal "production: always --maintenance --backup" \
    "--branch production --expect-sha $prod2 --expect-env staging --maintenance --backup" "$(cat "$GATE_TEST_ARGS")"
expect_equal "production: the backup ran" "yes" "$([[ -n "$(line_of "solava:db-backup")" ]] && echo yes || echo no)"

run_gate dev "deploy $dev2 --dry-run"
expect_equal "dev dry run: --dry-run passes through" "--branch main --expect-sha $dev2 --expect-env staging --dry-run" "$(cat "$GATE_TEST_ARGS")"
expect_equal "dev dry run: changes nothing" "$dev1" "$(git -C "$g/server-dev" rev-parse HEAD)"
expect_equal "dev dry run: deploy.sh's dry run output" "yes" "$(has "$gate_output" "Dry run done.")"

run_gate production "deploy $prod2 --dry-run"
expect_equal "production dry run: the backup flags stay, then --dry-run" \
    "--branch production --expect-sha $prod2 --expect-env staging --maintenance --backup --dry-run" "$(cat "$GATE_TEST_ARGS")"

run_gate dev "deploy $dev1"
expect_equal "exit 78 (a newer commit, skipped) passes through" "78" "$gate_code"

mkdir -p "$dev_lock"
echo "$$" >"$dev_lock/pid"
run_gate dev "deploy $dev2"
expect_equal "a live lock: busy (75)" "75" "$gate_code"
expect_equal "a live lock: deploy.sh never ran" "no" "$(deploy_ran)"
expect_equal "a live lock: it's left alone" "$$" "$(cat "$dev_lock/pid")"
expect_equal "the log says busy" "yes" "$(has "$(last_log)" "verdict=busy")"
run_gate production "deploy $prod2"
expect_equal "a lock on dev doesn't block production" "0" "$gate_code"
rm -f "$dev_lock/pid"
rmdir "$dev_lock"

true &
dead_pid=$!
wait "$dead_pid"
mkdir -p "$dev_lock"
echo "$dead_pid" >"$dev_lock/pid"
run_gate dev "deploy $dev2"
expect_equal "a stale lock (dead pid) is taken over" "0" "$gate_code"
expect_equal "and removed afterwards" "no" "$([[ -e "$dev_lock" ]] && echo yes || echo no)"

mkdir -p "$dev_lock"
run_gate dev "deploy $dev2"
expect_equal "a lock with no pid file yet: busy (75)" "75" "$gate_code"
touch -d '10 minutes ago' "$dev_lock"
run_gate dev "deploy $dev2"
expect_equal "a lock with no pid file, over 5 minutes old, is taken over" "0" "$gate_code"

mkdir -p "$dev_lock"
echo "$$" >"$dev_lock/pid"
touch -d '3 hours ago' "$dev_lock"
run_gate dev "deploy $dev2"
expect_equal "a lock over 2 hours old is taken over even if its pid runs (pid reuse)" "0" "$gate_code"

# Another gate in the middle of taking over (its guard is there): this one
# leaves the stale lock alone instead of removing it a second time.
dev_guard="$g/home/run/dev.takeover"
mkdir -p "$dev_lock" "$dev_guard"
echo "$dead_pid" >"$dev_lock/pid"
run_gate dev "deploy $dev2"
expect_equal "a takeover already in progress: busy (75)" "75" "$gate_code"
expect_equal "and the stale lock is left to that takeover" "$dead_pid" "$(cat "$dev_lock/pid" 2>/dev/null)"
expect_equal "and its guard too" "yes" "$([[ -d "$dev_guard" ]] && echo yes || echo no)"
touch -d '2 minutes ago' "$dev_guard"
run_gate dev "deploy $dev2"
expect_equal "a guard over a minute old (a killed takeover) is cleared, and the lock taken over" "0" "$gate_code"
expect_equal "with no guard or lock left behind" "no" "$([[ -e "$dev_guard" || -e "$dev_lock" ]] && echo yes || echo no)"

lines_before=$(wc -l <"$gate_log")
secret_value="s3cret-$RANDOM-must-not-be-logged"
GATE_TEST_SECRET="$secret_value" run_gate dev "deploy $dev2"$'\n'"id"
expect_equal "a refused command is logged as one line" "$((lines_before + 1))" "$(wc -l <"$gate_log")"
expect_equal "with its control characters stripped" "yes" "$(has "$(last_log)" "verdict=refused cmd=deploy ${dev2}id")"
GATE_TEST_SECRET="$secret_value" run_gate dev "deploy $dev2"
expect_equal "no environment value is ever logged" "no" "$(grep -q -- "$secret_value" "$gate_log" && echo yes || echo no)"
run_gate dev "deploy $dev2 $(printf 'x%.0s' {1..300})"
expect_equal "a long command is cut to 200 characters" "200" "$(last_log | sed 's/.* cmd=//' | tr -d '\n' | wc -c | tr -d ' ')"

# gate_conf_writable_case <chmod mode> <who>
gate_conf_writable_case() {
    chmod 600 "$conf"
    chmod "$1" "$conf"
    if (((8#$(stat -c %a "$conf") & 8#022) == 0)); then
        echo "  skip a gate.conf writable by $2 (this filesystem doesn't keep that permission)"
        chmod 600 "$conf"
        return
    fi
    run_gate dev "deploy $dev2"
    expect_equal "a gate.conf writable by $2 is refused (126)" "126" "$gate_code"
    expect_equal "a gate.conf writable by $2: deploy.sh never ran" "no" "$(deploy_ran)"
    expect_equal "a gate.conf writable by $2: says why" "yes" "$(has "$gate_output" "gate.conf can be written by others")"
    chmod 600 "$conf"
}
gate_conf_writable_case g+w "the group"
gate_conf_writable_case o+w "everyone"

echo ""
echo "${passed} passed, ${failed} failed"
((failed == 0))
