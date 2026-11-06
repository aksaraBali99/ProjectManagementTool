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
# The file's full rules live in App\Support\ReleaseNotes (and a test runs
# them on the real file); this checks the same shape with jq, plus what
# only the release knows: its version and its tasks.

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
        echo "::error::$notes is not valid JSON."
        return 1
    fi

    # Every rule on the file's shape, as one message per problem.
    local problems
    # shellcheck disable=SC2016 # $r, $v, ... are jq's own variables
    problems=$(jqr -r '
        def isversion: type == "string" and test("^v[0-9]{4}\\.[0-9]{2}\\.[0-9]+$");
        def key: ltrimstr("v") | split(".") | map(tonumber);
        def text: type == "string" and (gsub("\\s"; "") | length > 0);
        if (type != "object") or ((.releases | type) != "array") then
            "the file must be an object with a \"releases\" list"
        else
            .releases as $r
            | ( $r[] | (.version // "a release") as $v
                | ( if (.version | isversion) then empty else "\($v): \"version\" must look like v2026.11.1" end ),
                  ( if ((.date | type) == "string" and (.date | test("^[0-9]{4}-[0-9]{2}-[0-9]{2}$"))) then empty else "\($v): \"date\" must look like 2026-11-10" end ),
                  ( if (.summary | text) then empty else "\($v): \"summary\" is missing" end ),
                  ( if ((.tasks | type) == "array" and (.tasks | length) > 0) then
                        ( .tasks[] | (.number // "?") as $n
                          | ( if ((.number | type) == "number" and .number >= 1 and (.number | floor) == .number) then empty else "\($v): a task needs a \"number\"" end ),
                            ( if (.type | IN("Enhancement", "Bugfix", "Chore")) then empty else "\($v): task #\($n) needs a \"type\": Enhancement, Bugfix or Chore" end ),
                            ( if (.title | text) then empty else "\($v): task #\($n) needs a \"title\"" end ),
                            ( if (.type | IN("Enhancement", "Bugfix")) and ((.before | text) | not) then "\($v): task #\($n) needs \"before\"" else empty end ),
                            ( if (.type | IN("Enhancement", "Bugfix")) and ((.after | text) | not) then "\($v): task #\($n) needs \"after\"" else empty end ),
                            ( if (has("pr") and has("prs")) then "\($v): task #\($n) has both \"pr\" and \"prs\"" else empty end ) ),
                        ( [.tasks[].number] | group_by(.)[] | select(length > 1) | "\($v): task #\(.[0]) is listed more than once" )
                    else "\($v): \"tasks\" must be a non-empty list" end ) ),
              ( [$r[].version] | group_by(.)[] | select(length > 1) | "\(.[0]) appears more than once" ),
              ( [$r[] | .version | select(isversion)] as $vs
                | range(1; $vs | length) as $i
                | select(($vs[$i] | key) >= ($vs[$i - 1] | key))
                | "the releases must be newest first, but \($vs[$i]) comes after \($vs[$i - 1])" )
        end
    ' "$notes")

    if [[ -n "$problems" ]]; then
        while IFS= read -r line; do
            echo "::error::Release notes: $line."
        done <<<"$problems"
        return 1
    fi

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
        echo "::error::Release notes: $version is already a tag. The newest entry must be for a release that hasn't happened yet."
        errors=1
    else
        echo "OK: the newest entry is $version, the version this release will get."
    fi

    # The version comes from the deploy's month (Asia/Makassar); a planned
    # date in another month means the deploy may compute a different one.
    local version_month="${version:1:7}" date_month="${date:0:7}"
    if [[ "${date_month/-/.}" != "$version_month" ]]; then
        echo "::warning::Release notes: $version is planned for $date, another month. If it deploys in that month, the deploy will tag the next version of that month instead (it warns when it does)."
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
