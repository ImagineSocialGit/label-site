# Label Site — staging syndication queue contract

## Ownership

The Label staging/admin Laravel application accepts authenticated published-content
change notifications and runs authoritative Artist snapshot reconciliation. Its
production site is the public-serving plane, not an additional syndication
consumer. The webhook route is deliberately exempt from *browser CSRF only*;
token/source authentication in `PublishedContentChangedController` remains mandatory.

The 15-minute `artists:sync` command is a repair/fallback path, not the normal
notification queue. It runs synchronously under `schedule:run`; do not add it to
the notification worker. Future-dated content may require this fallback when
its publication eligibility changes solely because time passes.

## Staging connection contract

For the QH Music Group staging deployment, set (or verify) the following
values in its existing `.env`, without copying or printing credentials:

```dotenv
QUEUE_CONNECTION=database
DB_QUEUE=label_staging_qhmusicgroup
DB_QUEUE_RETRY_AFTER=180
CACHE_PREFIX=label_staging_qhmusicgroup_cache_
```

Use a **site-and-environment-specific** queue name and cache prefix. Do not
copy this queue name to another Label deployment, an Artist application, or
production. If staging/production share database infrastructure, never allow
a second deployment to consume this staging queue.

The database queue driver is already supported by `config/queue.php`; no
additional queue tables, Laravel packages, or Horizon installation are required.
The existing `jobs`, `failed_jobs`, and `cache` tables must be migrated on the
actual backing connection before switching on jobs. Confirm this with
`php artisan migrate:status` on staging rather than guessing the database.

The database driver must have `retry_after` longer than the worker `--timeout`.
For this Label syndication workload, 180s/120s is the initial contract; review
if reconciliation grows beyond that bound.

## Lightweight staging execution

Verify the actual PHP CLI path and application checkout before installing
server crontab entries. These are **examples** for the existing Iss-Dev layout;
review them against real staging paths/permissions first:

```cron
* * * * * cd /var/www/staging.qhmusicgroup.com/label-site && /usr/bin/php8.3 artisan schedule:run >> /dev/null 2>&1
* * * * * flock -n /tmp/label-staging-qhmusicgroup-queue.lock /bin/bash -c 'cd /var/www/staging.qhmusicgroup.com/label-site && /usr/bin/php8.3 artisan queue:work database --queue=label_staging_qhmusicgroup --stop-when-empty --sleep=1 --tries=5 --timeout=120' >> /var/www/staging.qhmusicgroup.com/label-site/storage/logs/queue-drain.log 2>&1
```

Use the deployment identity already authorized for that checkout. Verify its
runtime write permissions; do not change owners, groups, or ACLs by guesswork.
`flock` prevents cron from starting a second worker for the same site while
an existing drain is active. The worker exits when its queue is empty, so no
idle PHP process remains. This does **not** serialize workers for other sites;
monitor host load and adjust cadence if necessary.

`--queue` is explicit so an accidentally changed environment default cannot
cause this worker to drain a different queue. `DB_QUEUE` must match that name
so newly dispatched jobs reach the intended worker.

## Deployment / verification order

1. Merge and deploy the source changes through Git (never edit deployed PHP
   directly). Run `php artisan optimize:clear` in the deployed Label checkout.
2. Verify `QUEUE_CONNECTION`, `DB_QUEUE`, `DB_QUEUE_RETRY_AFTER`, `CACHE_PREFIX`,
   database connection, and PHP runtime **without exposing secrets**.
3. Check schema with `php artisan migrate:status` and apply only reviewed,
   compatible outstanding migrations. Staging/production may share schema.
4. Confirm a **single** staging scheduler and an isolated staging drain job;
   check user crontabs and system cron before adding entries.
5. Verify `php artisan schedule:list`, `php artisan queue:failed`, cron execution,
   and that `queue:work` does not consume unrelated site jobs.
6. Test one authenticated Artist callback against Label staging and verify the
   queued job reconciles the expected Artist revision.
7. Confirm an unauthorized callback is rejected. Confirm the 15-minute fallback
   still runs. Do not enable duplicate consumers in production.

Do not run `artists:sync` from both staging and production on a shared
publication database. If a callback endpoint is exposed on production, verify
routing/ingress policies before rollout; the desired reconciliation owner is
staging/admin.