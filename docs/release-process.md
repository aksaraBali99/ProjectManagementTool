# Release process

How Solava gets from a merged PR to DEV and to PROD (task #119).

- **main** is the integration branch. Every push to main, once CI passes, deploys to **DEV** by itself.
- **production** is what's live on **PROD**. It only changes through a pull request: a *release PR* from main, or a *hotfix* from a `Hotfix/<task>-<slug>` branch. Every push to production, once CI passes, waits for your approval, then deploys to PROD.
- Each successful PROD deploy is tagged **vYYYY.MM.N** (Bali time) and gets a GitHub Release listing its tasks and PRs.

The moving parts:

| File | What it does |
|---|---|
| `.github/workflows/ci.yml` | Tests, Pint, shellcheck and the script tests. Runs on every PR into main or production (the required `test` check), and inside Deploy. |
| `.github/workflows/deploy.yml` | On a push to main or production: CI, then deploy over SSH, smoke test, and (PROD) tag + Release. Also *Run workflow* by hand. |
| `.github/workflows/release-check.yml` | On PRs into production: the required `release-check` check (source branch, checklist, drift), plus the job summary to paste into the PR. Also labels release and back-merge PRs `release`. |
| `deploy.sh` | Runs on the server. Pre-flight checks, then (PROD) maintenance mode on and a backup, then fast-forward, composer, migrate, caches, and maintenance mode off. |
| `php artisan solva:db-backup` | The gzipped database dump every PROD deploy takes with the site down, just before the pull, in `storage/app/backups` (5 kept). Each file is read back and checked before older ones are deleted. |
| `.github/PULL_REQUEST_TEMPLATE/release.md` | The release PR template. |
| `.github/scripts/*.sh` | Version number, task list, release checks and release notes checks (the release notes ones need `jq`), tested by `tests/scripts/run.sh`. |
| `resources/release-notes.json` | The release notes, one entry per release, shown in Settings > Release notes (section 7). |

Assets are unchanged: `public/build` is still built locally (`npm run build`) and committed, because Hostinger can't run npm.

---

## 1. One-time setup

Do these in order. Nothing here is automated, and the PROD deploy stays switched off until step g.

### a. Create the production branch from what's live on PROD

Use the commit PROD is running now, **not** the tip of main (main has changes PROD hasn't had yet).

1. On the PROD server, in the app folder: `git rev-parse HEAD`. That is `<live-sha>`.
2. On your machine:
   ```
   git fetch origin
   git push origin <live-sha>:refs/heads/production
   git tag v2026.11.0 <live-sha>
   git push origin v2026.11.0
   ```
   `v2026.11.0` is the starting point: the first release's notes list everything after it.

### b. Point each server at its branch

**PROD**, in the app folder:
```
git fetch origin
git checkout production
git status            # must say "nothing to commit, working tree clean"
git config core.fileMode false   # if not set already: stops chmod noise showing as changes
```

**DEV**, in the app folder:
```
git checkout main
git pull --ff-only origin main
git status            # must be clean
git config core.fileMode false
```

If `git status` shows changed files, look at them (`git diff`) and decide before going on. The deploy refuses to run on a server with local changes.

### c. Rulesets (Settings > Rules > Rulesets)

**main**
- Require a pull request before merging.
- Require status checks: `test`.
- Block force pushes. Restrict deletions.
- Allowed merge methods: **Squash and Merge commit**. Squash is for every normal PR. A merge commit is for back-merge PRs only (see Hotfix), because a squash would give the hotfix a new commit ID and the drift check would keep failing.

**production**
- Require a pull request before merging.
- Require status checks: `test` and `release-check`.
- Block force pushes. Restrict deletions.
- Allowed merge methods: **Merge commit only**. A squash creates commits that aren't on main and trips the drift check.

**Both:** allow admin bypass, for emergencies only.

### d. Environments (Settings > Environments)

Create two:

| | dev | production |
|---|---|---|
| Deployment branches | Selected branches: `main` | Selected branches: `production` |
| Required reviewers | none | `aksaraBali99` |
| Prevent self-review | | **off** (you're the only reviewer) |

Then add these to **each** environment:

| Name | Kind | Value |
|---|---|---|
| `SSH_HOST` | variable | the server's SSH host (hPanel > Advanced > SSH Access) |
| `SSH_PORT` | variable | `65002` |
| `SSH_USER` | variable | the SSH username, e.g. `u123456789` |
| `APP_PATH` | variable | the app folder, absolute, e.g. `/home/u123456789/domains/<domain>/solava`. Must start with `/`, not `~` (run `pwd` in the app folder over SSH to get it) |
| `APP_URL` | variable | the site's address, e.g. `https://dev.example.com` (no trailing slash) |
| `EXPECTED_APP_ENV` | variable | that server's `APP_ENV` from its `.env`, exactly (check with `php artisan env` on the server) |
| `SSH_KEY` | secret | the private key of a key pair made only for this (below) |
| `SSH_KNOWN_HOSTS` | secret | the server's host key line (below) |

**SSH key**, one per environment, on your machine:
```
ssh-keygen -t ed25519 -N "" -C "github-deploy-dev" -f solava-deploy-dev
```
- Add `solava-deploy-dev.pub` in hPanel > SSH Access > SSH keys for that server's account.
- Paste the whole content of `solava-deploy-dev` (the private key) into the `SSH_KEY` secret.
- Then delete both files from your machine.
- Repeat with `-prod` for production.

If DEV and PROD are on the same Hostinger account, either key reaches both sites. `APP_PATH` and the `EXPECTED_APP_ENV` check still keep each deploy on its own site.

**Known hosts:**
```
ssh-keyscan -p 65002 <SSH_HOST>
```
- Check the fingerprint before trusting it. Run `ssh-keyscan -p 65002 <SSH_HOST> | ssh-keygen -lf -`, and compare it with what `ssh -p 65002 <user>@<host>` showed you when you first connected.
- Paste the `[host]:65002 ssh-ed25519 ...` line(s) into `SSH_KNOWN_HOSTS`.
- Host key checking is never switched off. If the server's key changes, the deploy fails until you update this secret.

### e. Merge commits allowed

Settings > General > Pull Requests: make sure **Allow merge commits** is ticked. Release PRs and back-merges need it. It's on today.

### f. Check DEV first

Actions > Deploy > Run workflow:
- *Use workflow from*: `main`
- environment: `dev`
- dry run: ticked

The log shows DEV's branch, the commit, `APP_ENV`, PHP's version and pending migrations. That confirms SSH from GitHub works. Until the dev environment's config is in, that run fails at once with "Missing config for this environment: ...", and nothing touches the server.

### g. Switch on PROD deploys, last

Leave the repository variable `PROD_DEPLOY_ENABLED` **unset** until DEV has deployed cleanly a few times. While it's unset, a push to production runs CI and stops; nothing reaches PROD.

When ready: Settings > Secrets and variables > Actions > Variables > New repository variable: `PROD_DEPLOY_ENABLED` = `true`. Then do the first release (below).

*Run workflow* from `production` (a dry run, or a re-deploy) only works once production contains `deploy.yml`, i.e. after the first release. Before that, the first-release steps include a dry run by hand on the server.

### First release (once only)

PROD's current checkout is older than `solva:db-backup`. On the very first release, the deploy's backup step stops it with "no 'solva:db-backup' command yet", before changing anything. For that one release:

1. Add its release notes entry first, as `v2026.11.1` (section 2, step 1): everything on main since the commit live on PROD. Then merge the release PR as usual (section 2). The Deploy run waits for your approval. **Don't approve yet.**
2. Check the latest hPanel backup completed (or take one in hPanel > Files > Backups).
3. On the PROD server, run that release's own `deploy.sh` by hand: a dry run first, then for real without `--backup`:
   ```
   cd <APP_PATH>
   git fetch origin production
   git show origin/production:deploy.sh > ~/.deploy-first.sh
   bash ~/.deploy-first.sh --branch production --expect-sha "$(git rev-parse origin/production)" --expect-env <APP_ENV> --dry-run
   bash ~/.deploy-first.sh --branch production --expect-sha "$(git rev-parse origin/production)" --expect-env <APP_ENV> --maintenance
   rm ~/.deploy-first.sh
   ```
4. Now approve the waiting run. This is also the first SSH from GitHub to PROD. It finds the code already in place, takes its backup, finds nothing to migrate, smoke-tests and creates **v2026.11.1** with its Release. If it fails at "Missing config" or at SSH, PROD is already deployed and fine: fix the config and use *Run workflow* from `production` (section 4) to get the tag.

From then on every release goes through the workflow alone.

---

## 2. A normal release

1. **Add the release notes.** On a branch from main, add the release's entry at the top of `resources/release-notes.json`: its version (the next `vYYYY.MM.N`), planned date, summary, and one item per task (section 7). Open a PR into main titled `Chore: release notes for vYYYY.MM.N`, merge it, and check Settings > Release notes on DEV once it's deployed there.
2. **Open the release PR** with the template:
   https://github.com/aksaraBali99/ProjectManagementTool/compare/production...main?template=release.md
3. **Title it** `Release vYYYY.MM.N`, with the version from the entry. It must start with `Release v`: the PR is then labelled `release` and left out of GitHub's generated list.
4. **Wait for the checks** (`test`, `release-check`). Open *release-check > Summary* and paste its task list and migrations into the PR. Its **Release notes** section must be all ✅: the entry's version is the one this release will get, and it lists every task in the release. Fix the entry on main (another small PR) if not; the release PR picks it up.
5. **Fill in the template:**
   - mark each migration safe / data-changing / slow;
   - list the before and after steps, or "None";
   - write the rollback plan.
6. **Tick every checklist box** once it's true. `release-check` fails while any box is empty, and re-runs when you edit the description.
7. **Merge** with **Create a merge commit**.
8. **Do the "before" steps**, if any.
9. **Approve the deploy:** Actions > the Deploy run > *Review deployments* > production > *Approve and deploy*.
10. **Watch the run.** It takes the site down, backs up the database, deploys and migrates, brings the site up, then smoke-tests `/up`. With the site down during the backup, nothing is written between the backup and the migrations. Maintenance mode lasts from before the backup until the caches are rebuilt: the backup's time plus usually under a minute.
11. **Do the "after" steps**, if any.
12. **Fill in the version** in the PR. It's the entry's version, unless the run warned that it had to use another one (it says why); it's in the run's summary and on the Releases page either way.

A release PR from main always contains everything on main. To leave something out, it has to be reverted on main first, or not merged yet.

## 3. A hotfix

For a fix that can't wait for the next release:

1. Branch from **production**, not main:
   ```
   git fetch origin
   git checkout -b Hotfix/<task>-<slug> origin/production
   ```
   The name must start with `Hotfix/` (capital H), or `release-check` fails.
2. Make the fix, and add its entry at the top of `resources/release-notes.json` **on the hotfix branch** (the next version, e.g. `v2026.11.4`). Open a PR into **production**. `test` and `release-check` run; the checklist and drift checks don't apply to hotfixes, but the release notes check does.
3. Merge it with **Create a merge commit**, then approve the deploy. It's tagged with the entry's version.
4. **Straight away**, open a PR from **production** into **main**, titled `Back-merge v2026.11.4 into main`, and merge it with **Create a merge commit** (not squash). That also brings the hotfix's notes entry to main. Until this is merged, the drift check blocks the next release PR.

## 4. Re-deploying the same commit

After fixing a server's `.env`, for example. Not after a rollback (section 5): that would deploy the failed commit again.

- Actions > Deploy > Run workflow, with *Use workflow from* set to the branch: `main` for dev, `production` for production.
- environment: `dev` or `production`; dry run: unticked.

PROD still asks for approval. A re-deploy of a commit that's already tagged gets no new tag.

Run from the wrong branch (e.g. environment `production` from `main`), the run fails at once with a message saying which branch to use.

## 5. Rollback

**Code.** Never by force-pushing production. Either:
- **Revert PR:** `git revert` the bad change on a branch from main, merge it into main (it deploys to DEV), then do a normal release.
- **Hotfix:** for a quick fix on PROD (section 3), if waiting for a release isn't an option.

**Data.** Restore the backup the deploy took with the site down, just before pulling and migrating. It's the newest file in `storage/app/backups` (the deploy log prints its name), or use an hPanel backup.

> A restore puts the whole database back to the moment of the backup. **Anything written after it is lost**: tasks, comments, uploads' records, logins. Do it only when the data is actually wrong, not just to undo code.

A backup taken right before a release goes with the code that was live before that release, so the code goes back to that commit too. Two commit IDs are needed below, both printed at the top of the failed deploy's log:
- `<live-sha>`: the "now at" line, the code that was live before the release;
- `<failed-sha>`: the "deploy" line, the release that failed.

On the PROD server, in the app folder:

1. **Take the site down, put the code back, and pick the backup.** (After a failed PROD deploy the site is already down.)
   ```
   php artisan down
   git reset --hard <live-sha>
   composer install --no-dev --optimize-autoloader
   php artisan view:clear && php artisan config:clear && php artisan route:clear
   php artisan config:cache && php artisan route:cache
   ls -lt storage/app/backups                      # pick the file, newest first
   grep -E '^DB_(HOST|PORT|DATABASE|USERNAME)=' .env
   ```
2. **Restore it.**
   ```
   gunzip -c storage/app/backups/db-YYYYmmdd-HHMMSS.sql.gz \
     | mariadb --host=<DB_HOST> --port=<DB_PORT> --user=<DB_USERNAME> -p <DB_DATABASE>
   ```
3. **Drop the tables the failed release's migrations created.**
   - The dump only holds the tables that existed when it was taken. It puts those back exactly as they were, including any columns or indexes the failed release added to them. Tables the failed release *created* are not in the dump, so they are left behind.
   - Left in place, they make the release's migration fail with "table already exists" the next time it runs.
   - List the migrations the release added, and the tables they create:
     ```
     git diff --name-only --diff-filter=A <live-sha> <failed-sha> -- database/migrations
     git diff --diff-filter=A <live-sha> <failed-sha> -- database/migrations | grep -oE 'Schema::create\(.[A-Za-z0-9_]+' | sort -u
     ```
   - Read each listed migration file too. If it does anything else to the database outside an existing table (renames a table, for example), undo that by hand as well.
   - Drop the tables, in one statement with foreign-key checks off, so their order doesn't matter:
     ```
     mariadb --host=<DB_HOST> --port=<DB_PORT> --user=<DB_USERNAME> -p <DB_DATABASE> \
       -e "SET FOREIGN_KEY_CHECKS=0; DROP TABLE IF EXISTS <table_1>, <table_2>; SET FOREIGN_KEY_CHECKS=1;"
     ```
   - If the second `git diff` lists nothing, the release created no tables; skip the drop.
4. **Check the database matches the live code.**
   ```
   php artisan migrate:status
   ```
   Every migration must say **Ran**, and none **Pending**. Anything else means the schema and the live code disagree. Leave the site down and sort that out before going on.
5. **Bring the site back up.**
   ```
   php artisan up
   ```

**After rolling back, don't re-run the failed deploy.**
- The branch still has the failed commit. Re-running Deploy (section 4), or approving a run for that commit that's still waiting, fast-forwards the server straight back onto it and runs its migrations again. Cancel any such waiting run in Actions.
- The server stays on `<live-sha>` until the fix ships as a **new commit on top**:
  - a revert release: a revert PR into main, then a normal release (section 2);
  - or a hotfix (section 3).
- That new commit deploys normally: the server fast-forwards from `<live-sha>` past the failed commit to the fix, and the failed release's migrations run only if the fix still contains them.

`-p` asks for the database password (`DB_PASSWORD` in `.env`). Type it at the prompt; never put it on the command line.

## 6. When a deploy fails

GitHub emails you, and the run is red. Open the run, then the failed job (*deploy-dev* or *deploy-prod*), then the *Deploy* step.

- **"Missing config for this environment"**: a variable or secret isn't set (section 1d). Nothing ran on the server.
- **"APP_PATH must be the full path on the server"**: `APP_PATH` starts with `~` or is a relative path. Set it to the full path, starting with `/` (run `pwd` in the app folder over SSH to get it). Nothing ran on the server.
- **"DEPLOY STOPPED before changing anything"**: a pre-flight check failed. The message says which one: wrong branch, local changes, wrong `APP_ENV`, a checkout that can't fast-forward, or a commit that isn't on the branch (the branch was rewritten or moved backwards). The site is untouched and still up. Fix the cause, then re-run (section 4).
- **"the database backup failed … Nothing was pulled or migrated, and the site is up"**: the backup ran with the site down and failed. The script brought the site back up, unchanged. Check the space left on the server (hPanel > Files) and the backup's error above it, then re-run. If it also says **"'php artisan up' failed too: THE SITE IS STILL DOWN"**, SSH in and run `php artisan up`. Nothing else needs undoing.
- **"!!! DEPLOY FAILED at step: ..."**: something failed after the code started changing. The log says which step and prints the exact commands to finish the deploy or go back.
  - **PROD (maintenance mode on): the site stays down, on purpose**, so a half-migrated database doesn't take traffic. Visitors see the maintenance page. To bring it back:
    1. SSH in and `cd` to the app folder.
    2. Read `storage/logs/laravel.log` and the job log.
    3. Either finish the deploy with the printed commands (often just re-running the failed one), or go back to the previous code with the printed `git reset --hard <old-sha>`, `composer install` and cache commands (and restore the backup if a migration half-ran, section 5). Once gone back, don't re-run the deploy; the fix ships as a revert release or a hotfix (section 5).
    4. Only when the site works: `php artisan up`.
  - **DEV:** there's no maintenance mode, so the site may be half-deployed until you fix it the same way, or until the next merge deploys over it.
- **"Skipped: a newer commit is on ..."**: not a failure. Two merges landed close together, and the later run deploys the newer commit.
- **Smoke test failed**: the deploy finished, but `/up` didn't answer 200 within 50 s. Check the site and `storage/logs/laravel.log`. No tag is created for PROD.

No tag and no Release are created unless the deploy and the smoke test both pass.

**A release PR squash-merged by mistake** (if a ruleset was missing): the next release's drift check fails, listing the commit. Fix it with a back-merge PR from production into main, merged with a merge commit (section 3, step 4).

## 7. Release notes

**Settings > Release notes** (owner and super_admin) lists every release, newest first, with what changed for people: per task, its title and a before / after. It reads `resources/release-notes.json`, which ships with the code it describes: each server lists the notes of the code it runs, so DEV shows a release before PROD does. Nothing is stored in the database or fetched from GitHub.

**The file**, newest entry first:

```json
{
  "releases": [
    {
      "version": "v2026.11.2",
      "date": "2026-11-20",
      "summary": "One or two sentences on the release.",
      "tasks": [
        {
          "number": 123,
          "type": "Bugfix",
          "title": "Make a deactivated task read-only",
          "before": "What people saw or could do before.",
          "after": "What changes for them now.",
          "pr": 152
        }
      ]
    }
  ]
}
```

- `version` is `vYYYY.MM.N`: the version the release **will** get. Versions are unique and newest first.
- `date` is the planned release date, `YYYY-MM-DD`.
- `type` is `Enhancement`, `Bugfix` or `Chore`. `before` and `after` are required for Enhancement and Bugfix, and optional for Chore.
- `pr` is one PR number, or `prs` a list of them (not both). A task number appears once per entry.
- **Plain text only**, line breaks allowed: the page shows it exactly as written, never as HTML. The file is shipped to PROD, so plain change descriptions only: no secrets, no internal hostnames.

**The checks:**
- A test checks the real file on every PR (`ReleaseNotesPageTest`), so a bad edit fails CI.
- **release-check** (on release and hotfix PRs into production) checks that the newest entry's version is the one this release will get and isn't already a tag, and that it lists every `task #N` from the release's commits. Extra tasks in the entry only warn. If the entry's planned date is in another month than its version, it warns: the deploy still tags the entry's version (a November version deployed in December stays `v2026.11.N`), so for a version of the later month, update the entry and open the release PR in that month.
- An entry for a release that never happened (its release PR closed) is reused for the next release: the version check shows it, since the next release gets the same version.

**The GitHub Release** for each PROD deploy is tagged with the newest entry's version, if it's a `vYYYY.MM.N`, isn't already a tag and is higher than the previous tag. Otherwise it gets the next version as before, and the run warns why. The Release starts with the entry's notes (**Release notes**: summary and each task's title and "after"), then **Tasks in this release** (the `task #N` references from the commit subjects since the previous tag), then GitHub's generated list of merged PRs under **Changes**, without release and back-merge PRs.

## Edge cases, for reference

| What happens | Result |
|---|---|
| Two merges to main within a minute | The second deploy queues behind the first (one deploy per server at a time). The first's pre-flight sees the newer commit and skips; the second deploys it. Nothing untested is deployed. If the second's CI fails, neither deploys, and GitHub emails you about that CI failure. |
| The branch no longer contains the commit being deployed (rewritten or moved backwards) | Pre-flight stops with an error, not a quiet skip. |
| A migration fails on PROD | Site stays in maintenance, job fails, no tag. Section 6. |
| The PROD approval is never given | The run waits; GitHub gives up after 30 days. Nothing happens on the server. |
| `deploy.sh` run by hand on the wrong server or branch | Stopped by the branch and `--expect-env` checks before anything changes. |
| Local changes on a server (e.g. chmod noise) | Pre-flight stops with the list of files. |
| Month rollover | Numbering restarts: `v2026.12.1`. |
| A PR into production from any branch other than main or `Hotfix/...` | `release-check` fails. |
