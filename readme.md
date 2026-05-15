# NextDeveloper IPaaS Library

This library provides an enterprise-level abstraction layer for Integration Platform as a Service (iPaaS) providers. It gives your application a unified interface to manage, synchronise, and monitor workflow automations across multiple third-party automation platforms through a single, consistent API.

The design goal is simplicity at the integration boundary — you register a provider once and the library handles authentication, data normalisation, periodic sync, and execution tracking for all supported platforms.

## Supported Platforms

| Platform | Type | Workflows | Executions | Activate / Deactivate |
|---|---|---|---|---|
| [n8n](https://n8n.io) | self-hosted / cloud | ✅ | ✅ | ✅ |
| [Make.com](https://make.com) | cloud | ✅ (scenarios) | ✅ | ✅ |
| [Zapier](https://zapier.com) | cloud | ✅ (zaps) | ❌ (not in public API) | ❌ (not in public API) |

## Core Concepts

### Providers
A `Provider` record represents a registered automation platform account. Each provider stores the credentials and base URL needed to reach the platform's API:

| Provider field | n8n | Make.com | Zapier |
|---|---|---|---|
| `region` / `base_url` | Instance URL | Regional API base | `https://api.zapier.com/v2` |
| `external_account_id` | N8N API key | Make.com team ID | — |
| `api_token` | — | Make.com API token | Zapier personal token |
| `provider_type` | `n8n` or `n8n-cloud` | `make` | `zapier` |

### Workflows
A `Workflow` is the library's unified representation of an automation (n8n workflow, Make scenario, Zapier zap). Workflows are fetched from the platform and stored locally so they can be queried, filtered, and associated with executions without hitting the upstream API on every request.

### Workflow Executions
A `WorkflowExecution` records the outcome of a single workflow run. The library normalises each platform's status vocabulary into a common set: `success`, `error`, `running`, `waiting`, `canceled`.

### Statistics
The library automatically maintains daily aggregate tables (`ipaas_workflow_daily_stats`, `ipaas_execution_daily_stats`) and per-account summaries (`ipaas_account_stats`) so dashboards can be served without expensive live queries.

---

## Platform Integration Features

- [x] Unified `WapInterface` — all platforms implement the same contract
- [x] `testConnection()` — verify credentials before storing a provider
- [x] `listWorkflows()` / `getWorkflow()` — browse automation definitions
- [x] `activateWorkflow()` / `deactivateWorkflow()` — control workflow state
- [x] `listExecutions()` / `getExecution()` / `deleteExecution()` — manage execution history
- [x] `syncWorkflows()` — pull all workflows from the platform and upsert locally
- [x] `syncExecutions()` — pull execution history within a configurable time window
- [ ] Webhook-triggered sync (currently polling only)
- [ ] Workflow creation via API (create automations from within your app)
- [ ] Cross-platform workflow migration

---

## Sync Jobs and Artisan Commands

The library ships with queue jobs and Artisan commands for each platform so syncs can be scheduled, dispatched manually, or run inline for debugging.

### Commands

```bash
# Sync all n8n providers (workflows + executions)
php artisan ipaas:sync-n8n

# Sync only workflows for a specific provider
php artisan ipaas:sync-n8n --workflows-only --provider=<uuid>

# Sync executions from a custom time window
php artisan ipaas:sync-n8n --executions-only --since="7 days ago"

# Run synchronously in the current process (no queue)
php artisan ipaas:sync-n8n --sync

# Make.com
php artisan ipaas:sync-make
php artisan ipaas:sync-make-scenarios
php artisan ipaas:sync-make-executions

# Zapier
php artisan ipaas:sync-zapier
php artisan ipaas:sync-zapier-workflows
```

### Scheduler
Add the following to your `app/Console/Kernel.php` to keep data fresh:

```php
$schedule->job(new SyncN8NWorkflowsJob())->hourly()->onQueue('ipaas-sync');
$schedule->job(new SyncN8NExecutionsJob())->hourly()->onQueue('ipaas-sync');
$schedule->job(new SyncMakeScenariosJob())->hourly()->onQueue('ipaas-sync');
$schedule->job(new SyncMakeExecutionsJob())->hourly()->onQueue('ipaas-sync');
$schedule->job(new SyncZapierWorkflowsJob())->hourly()->onQueue('ipaas-sync');
```

Jobs are dispatched to the `ipaas-sync` queue by default and support automatic retries with backoff.

---

## Authorization

The library uses role-based access control via the `IntegrationPlatformUser` role. This role restricts users to their own account's providers, workflows, and executions through Eloquent global scopes, so no data leaks across account boundaries.

---

## Perspectives (Read-Only Views)

The library exposes several database views for efficient reporting without application-level joins:

| Perspective | Description |
|---|---|
| `AccountProviderOverviews` | Per-account summary of registered providers and their health |
| `PlatformHealthPerspective` | Cross-provider health snapshot for operations dashboards |
| `WorkflowExecutionsPerspective` | Enriched execution list with workflow and provider context |

---

## Adding a New Platform

1. Create an integration class in `src/Integrations/<Platform>/` that extends `AbstractWapIntegration` and implements `WapInterface`.
2. Create a corresponding interface in the same directory.
3. Add the provider type to `WapIntegrationFactory::make()`.
4. Add sync jobs under `src/Jobs/<Platform>/` following the existing pattern.
5. Add Artisan commands under `src/Console/Commands/`.

---

## Commercial Support

Please let us know if you need any commercial support. We will be happy to help you integrate this library into your project.

## Want to Contribute?

You are very welcome to contribute. Please send us an email so we can get in touch and discuss the details:

support@plusclouds.com
