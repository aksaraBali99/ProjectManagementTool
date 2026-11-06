<!--
Release PR: main into production. Title: "Release vYYYY.MM.N" (the number
is only known after the deploy; "Release" + the date is fine until then,
but keep "Release v" at the start so it's left out of the release notes).
Merge with "Create a merge commit", never squash. Runbook:
docs/release-process.md.
-->

## Version

vYYYY.MM.N <!-- filled in after the deploy, from the Deploy run or the Releases page -->

## Tasks and PRs included

<!-- Paste from the release-check job's summary (Checks > release-check > Summary). -->

## Migrations in this release

<!--
One line each, from the same summary:
- `2026_11_03_000000_add_task_codes.php`: data-changing; can be undone with down(), data restored from the backup
Mark each one safe / data-changing / slow, and say whether it can be undone.
Or: None
-->

## Special steps

**Before the deploy** (in order):

<!-- Backfills, verify SQL, .env changes, cron changes... or "None" -->

**After the deploy** (in order):

<!-- ...or "None" -->

## Checklist

All must be ticked before merging (release-check fails while any box is empty).

- [ ] Every change here was tested on DEV at this exact commit
- [ ] Release notes entry added for this version (Settings > Release notes on DEV)
- [ ] Migrations reviewed; data-changing ones have a written rollback or restore plan
- [ ] The last nightly hPanel backup completed (the deploy also takes its own backup right before migrating)
- [ ] .env / config changes needed on PROD are done, or there are none
- [ ] Special steps listed above, or "None"
- [ ] Deploy time chosen outside busy hours

## Rollback plan

<!--
Code: a revert PR into main, then a release, or a hotfix (docs/release-process.md, "Rollback").
Data: restore storage/app/backups/db-<time>.sql.gz taken by this deploy (loses anything written after it).
Anything specific to this release:
-->
