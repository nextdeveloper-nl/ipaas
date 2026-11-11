# Stats & Health

IPAAS maintains rollup tables and read-only views so dashboards can answer "is our automation actually working?" without expensive live queries against raw execution history.

## Key Capabilities

- See, per account, how many providers/workflows exist and how today is going (executions, successes, errors, success rate)
- See daily history per provider and per workflow, not just today
- See workflow-level duration metrics (average/max), not just pass/fail counts
- Get one cross-provider health snapshot for an operations dashboard
- Get an enriched, pre-joined execution feed for reporting

## Per-Account Rollup — AccountStats

`ipaas_account_stats` holds one row per account: `total_providers`, `total_workflows`, `executions_today`, `success_today`, `errors_today`, `success_rate_today`. This is the "today" snapshot — for history over time, see the daily aggregates below. It's a pure read model; nothing in application code writes to it directly, so treat it as backed by a scheduled refresh (view/materialization) rather than something updated inline on every execution.

## Daily Aggregates

- **`ipaas_execution_daily_stats`** — one row per account + provider + day: `stat_date`, `provider_name`, `provider_type`, `total_executions`, `success_count`, `error_count`, `canceled_count`, `success_rate`. This is what a "last 30 days" chart per provider is built from.
- **`ipaas_workflow_daily_stats`** — one row per workflow + day: `stat_date`, `total_executions`, `success_count`, `error_count`, `canceled_count`, `avg_duration_ms`, `max_duration_ms`. This is the only place duration is tracked over time — useful for spotting a workflow that's gradually slowing down before it starts timing out.

## Health & Overview Perspectives

- **`PlatformHealthPerspective`** — cross-provider health snapshot: `is_default_wap`, `active_workflows`, `executions_today`/`success_today`/`errors_today`/`running_today`, `success_rate_today`, `last_execution_at`, `last_execution_status`, `health_status`. This is the one view built specifically for an ops dashboard — "which connections need attention right now."
- **`AccountProviderOverviews`** — a provider-connection-centric view: adds `base_url`, `total_automation_engines`, `provider_updated_at` alongside the same today-stats fields, for a per-account "what's connected and how's it doing" screen.
- **`WorkflowExecutionsPerspective`** — see [Workflows & Executions](workflows-and-executions.md); the enriched execution feed used for detailed reporting rather than health-at-a-glance.

## API Examples

**Get today's rollup for the current account**

```
GET /ipaas/account-stats
```

**Chart executions per provider over the last 30 days**

```
GET /ipaas/execution-daily-stats?filter[ipaas_provider_id]=a1b2...-uuid&filter[stat_date][gte]=2026-08-14
```

**Spot a workflow that's slowing down**

```
GET /ipaas/workflow-daily-stats?filter[ipaas_workflow_id]=c3d4...-uuid&sort=-stat_date
```

**Ops dashboard: which connections are unhealthy right now**

```
GET /ipaas/platform-health-perspective?filter[health_status]=degraded
```

## Related Features

- [Sync Jobs & Commands](sync-jobs-and-commands.md) — the sync activity these numbers are derived from
- [Sales & Marketing Automation](sales-and-marketing-automation.md) — why proving an automation is healthy matters for anything sales/marketing depends on
