# Sync Jobs & Commands

Workflow and execution data reaches the platform in one of two ways: **polling** (scheduled jobs and on-demand Artisan commands, for all three platforms) or, for self-hosted n8n only, **real-time execution hooks** pushed the moment a workflow starts or finishes.

## Key Capabilities

- Pull all workflows/executions for a platform on a schedule, per-platform
- Run a sync manually — for one provider, for a custom time window, inline or queued — for debugging or backfill
- For self-hosted n8n, get near-real-time execution notifications instead of waiting for the next poll

## Sync Jobs (Queued)

Each platform has its own sync jobs, dispatched to the `ipaas-sync` queue with automatic retries/backoff:

- **n8n** — `SyncN8NWorkflowsJob`, `SyncN8NExecutionsJob`
- **Make.com** — `SyncMakeScenariosJob`, `SyncMakeExecutionsJob`
- **Zapier** — `SyncZapierWorkflowsJob` (no execution sync — Zapier's public API doesn't expose execution history)

None of these are scheduled inside the module itself — `IPaasServiceProvider` registers the sync commands but has no `bootSchedule()`/cron wiring, so the host application's own scheduler must call them, for example:

```php
$schedule->job(new SyncN8NWorkflowsJob())->hourly()->onQueue('ipaas-sync');
$schedule->job(new SyncN8NExecutionsJob())->hourly()->onQueue('ipaas-sync');
$schedule->job(new SyncMakeScenariosJob())->hourly()->onQueue('ipaas-sync');
$schedule->job(new SyncMakeExecutionsJob())->hourly()->onQueue('ipaas-sync');
$schedule->job(new SyncZapierWorkflowsJob())->hourly()->onQueue('ipaas-sync');
```

## Artisan Commands (Manual / On-Demand)

```bash
# n8n — sync all providers (workflows + executions)
php artisan ipaas:sync-n8n

# n8n — workflows only, for one provider
php artisan ipaas:sync-n8n --workflows-only --provider=<uuid>

# n8n — executions from a custom time window
php artisan ipaas:sync-n8n --executions-only --since="7 days ago"

# n8n — run inline instead of queuing (useful for debugging)
php artisan ipaas:sync-n8n --sync

# Make.com
php artisan ipaas:sync-make
php artisan ipaas:sync-make-scenarios
php artisan ipaas:sync-make-executions

# Zapier
php artisan ipaas:sync-zapier
php artisan ipaas:sync-zapier-workflows
```

## Real-Time n8n Execution Hooks

For a **self-hosted** n8n instance, polling can be supplemented (or replaced for latency-sensitive alerting) with n8n's `ExecutionLifecycleHooks` — infrastructure-level, zero changes to any individual workflow. Configured once via `EXTERNAL_HOOK_FILES`, it fires a webhook to the platform on:

- `workflowExecuteBefore` — a workflow execution starts
- `workflowExecuteAfter` — a workflow execution finishes (success, error, or cancelled)

The full setup — hooks file, Docker Compose config, webhook receiver, and troubleshooting — is documented in [`n8n-execution-hooks-setup.md`](../n8n-execution-hooks-setup.md). This only applies to self-hosted n8n; n8n Cloud, Make, and Zapier have no equivalent, so they rely on the scheduled polling jobs above.

## API Note

There's currently no public `/ipaas` endpoint to trigger a sync directly — syncing happens via the queue jobs or Artisan commands above, not through the HTTP API.

## Related Features

- [Providers & Platforms](providers-and-platforms.md) — what a sync job actually pulls from
- [Workflows & Executions](workflows-and-executions.md) — what gets written locally by a sync
- [Stats & Health](stats-and-health.md) — what gets recomputed once new execution data lands
