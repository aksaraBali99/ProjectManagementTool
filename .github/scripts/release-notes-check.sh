#!/usr/bin/env bash
#
# task #124: release-check's checks on resources/release-notes.json for a
# pull request into production (a release or a hotfix).
#
# Usage: bash release-notes-check.sh <notes.json> <next-version> <tags-file> <tasks-file>
#
#   <next-version>  the version this release will get (next-version.sh)
#   <tags-file>     the existing tags, one per line
#   <tasks-file>    the task numbers in this release, one per line
#                   (from release-tasks.sh)
#
# Prints one line per finding: "::error::...", "::warning::..." or
# "OK: ...". Exits 1 when there's an error, 2 when jq is missing.
#
# The file's rules are the same as App\Support\ReleaseNotes's (the page
# rejects what this rejects; tests/scripts/run.sh and ReleaseNotesPageTest
# cover the same cases), plus what only the release knows: its version and
# its tasks.

set -euo pipefail
# shellcheck disable=SC1091 # release-notes-lib.sh, next to this script (checked on its own)
source "$(dirname "${BASH_SOURCE[0]}")/release-notes-lib.sh"

main() {
    if (($# != 4)); then
        echo "usage: release-notes-check.sh <notes.json> <next-version> <tags-file> <tasks-file>" >&2
        return 2
    fi
    require_jq

    local notes="$1" next="$2" tags_file="$3" tasks_file="$4"
    local errors=0

    if ! jqr -e . "$notes" >/dev/null 2>&1; then
        echo "::error::Release notes: $notes is not valid JSON."
        return 1
    fi

    # Every rule on the file's shape, one message per problem. Each item's
    # type is checked before its fields are read, so a malformed file gives
    # a message rather than a jq error.
    local problems jq_error="${TMPDIR:-/tmp}/release-notes-jq-error.$$"
    # shellcheck disable=SC2016 # $r, $v, ... are jq's own variables
    if ! problems=$(jqr -r '
        def isversion: type == "string" and test("^v[0-9]{4}\\.[0-9]{2}\\.[0-9]+$");
        def text: type == "string" and (gsub("\\s"; "") | length > 0);
        # Optional text: absent (or null), or text.
        def optionaltext: . == null or text;
        # A whole number, 1 or more, written without a decimal point
        # (5.0 is refused, as PHP decodes it as a float).
        def wholenumber: type == "number" and . >= 1 and (tostring | test("^[0-9]+$"));
        def leap: (. % 4 == 0 and . % 100 != 0) or . % 400 == 0;
        def realdate: type == "string" and test("^[0-9]{4}-[0-9]{2}-[0-9]{2}$")
            and (split("-") | map(tonumber) as [$y, $m, $d]
                | $m >= 1 and $m <= 12 and $d >= 1
                  and $d <= ([31, (if ($y | leap) then 29 else 28 end), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][$m - 1]));
        def task($v):
            if type != "object" then "\($v): every task must be an object"
            else (if (.number | wholenumber) then "task #\(.number)" else "a task" end) as $t
                | ( if (.number | wholenumber) then empty else "\($v): a task needs a \"number\" (a whole number, 1 or more)" end ),
                  ( if (.type | IN("Enhancement", "Bugfix", "Chore")) then empty else "\($v): \($t) needs a \"type\": Enhancement, Bugfix or Chore" end ),
                  ( if (.title | text) then empty else "\($v): \($t) needs \"title\" (text)" end ),
                  ( ["before", "after"][] as $field
                    | if (.type | IN("Enhancement", "Bugfix")) then
                          (if (.[$field] | text) then empty else "\($v): \($t) needs \"\($field)\" (text)" end)
                      elif (.[$field] | optionaltext) then empty
                      else "\($v): \($t): \"\($field)\" must be text" end ),
                  ( if (has("pr") and has("prs")) then "\($v): \($t) has both \"pr\" and \"prs\"; use one"
                    elif has("pr") then (if (.pr | wholenumber) then empty else "\($v): \($t): a PR number must be a whole number, 1 or more" end)
                    elif has("prs") then
                        (if (.prs | type) != "array" then "\($v): \($t): \"prs\" must be a list of PR numbers"
                         elif (.prs | all(wholenumber)) then empty
                         else "\($v): \($t): a PR number must be a whole number, 1 or more" end)
                    else empty end )
            end;
        if (type != "object") or ((.releases | type) != "array") then
            "the file must be an object with a \"releases\" list"
        else
            .releases as $r
            | ( $r | to_entries[] | .key as $i | .value
                | if type != "object" then "release #\($i + 1) must be an object"
                  else (if (.version | isversion) then .version else "release #\($i + 1)" end) as $v
                    | ( if (.version | isversion) then empty else "\($v) needs a \"version\" like v2026.11.1" end ),
                      ( if (.date | realdate) then empty else "\($v) needs a \"date\" like 2026-11-10" end ),
                      ( if (.summary | text) then empty else "\($v) needs \"summary\" (text)" end ),
                      ( if ((.tasks | type) == "array" and (.tasks | length) > 0) then
                            ( .tasks[] | task($v) ),
                            ( [.tasks[] | objects | .number | select(wholenumber)] | group_by(.)[] | select(length > 1) | "\($v) lists task #\(.[0]) more than once" )
                        else "\($v) needs a non-empty \"tasks\" list" end )
                  end ),
              ( [$r[] | objects | .version | select(isversion)] | group_by(.)[] | select(length > 1) | "\(.[0]) appears more than once" )
        end
    ' "$notes" 2>"$jq_error"); then
        echo "::error::Release notes: the file couldn't be checked ($(tr -d '\r' <"$jq_error" | head -n 1))."
        rm -f "$jq_error"
        return 1
    fi
    rm -f "$jq_error"

    if [[ -n "$problems" ]]; then
        while IFS= read -r line; do
            echo "::error::Release notes: $line."
        done <<<"$problems"
        return 1
    fi

    # Newest first: each version lower than the one before it, compared as
    # numbers (release-notes-lib.sh's version_key, the same order
    # next-version.sh and release-version.sh use).
    local previous_version="" current
    while IFS= read -r current; do
        if [[ -n "$previous_version" && ! "$(version_key "$current")" < "$(version_key "$previous_version")" ]]; then
            echo "::error::Release notes: the releases must be newest first, but $current comes after $previous_version."
            return 1
        fi
        previous_version="$current"
    done < <(jqr -r '.releases[].version' "$notes")

    local version date
    version=$(jqr -r '.releases[0].version // ""' "$notes")
    date=$(jqr -r '.releases[0].date // ""' "$notes")

    if [[ -z "$version" ]]; then
        echo "::error::Release notes: there's no entry for $next. Add it on main first (docs/release-process.md, section 2)."
        return 1
    fi

    if [[ "$version" != "$next" ]]; then
        echo "::error::Release notes: the newest entry is $version, but this release will be $next. Update the entry's version (an entry for a release that never happened is reused for the next one)."
        errors=1
    elif grep -qxF "$version" <(tr -d '\r' <"$tags_file"); then
        # Can't happen while <next-version> comes from next-version.sh (one
        # above the highest tag); kept as a guard should it come from
        # anywhere else.
        echo "::error::Release notes: $version is already a tag. The newest entry must be for a release that hasn't happened yet."
        errors=1
    else
        echo "OK: the newest entry is $version, the version this release will get."
    fi

    # The deploy tags the entry's version even in a later month (only a
    # version that's already a tag, or not above the previous one, falls
    # back), so a planned date in another month only warns.
    local version_month="${version:1:7}" date_month="${date:0:7}"
    if [[ "${date_month/-/.}" != "$version_month" ]]; then
        echo "::warning::Release notes: $version is planned for $date, another month. The deploy still tags it $version (it only falls back when that's already a tag, or not above the previous one). For a ${date_month/-/.} version instead, update the entry and open the release PR in that month."
    fi

    local listed missing="" extra=""
    listed=$(jqr -r '.releases[0].tasks[].number' "$notes" | sort -n)
    local in_release
    in_release=$(tr -d '\r' <"$tasks_file" | grep -E '^[0-9]+$' | sort -n -u || true)

    local number
    for number in $in_release; do
        if ! grep -qx "$number" <<<"$listed"; then missing+=" #$number"; fi
    done
    for number in $listed; do
        if ! grep -qx "$number" <<<"$in_release"; then extra+=" #$number"; fi
    done

    if [[ -n "$missing" ]]; then
        echo "::error::Release notes: $version doesn't list these tasks that are in the release:$missing."
        errors=1
    else
        echo "OK: every task in the release is in $version's notes."
    fi
    if [[ -n "$extra" ]]; then
        echo "::warning::Release notes: $version lists tasks no commit in the release mentions:$extra. Fine if they're there on purpose."
    fi

    return "$errors"
}

main "$@"
