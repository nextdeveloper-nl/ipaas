# Workflows & Executions

A **Workflow** is IPAAS's unified representation of one automation — an n8n workflow, a Make scenario, or a Zapier zap — regardless of which platform it's actually built on. A **WorkflowExecution** is one run of it.

## Key Capabilities

- Browse and query automations locally without hitting the upstream platform's API on every request
- Activate/deactivate a workflow from the platform (where the source platform's API supports it)
- See every run of a workflow, with its outcome normalized to one common set of statuses across all three platforms
- Enrich the raw execution list with workflow and provider context for reporting, without extra joins

## Workflows

A Workflow is fetched from the connected platform and stored locally so it can be listed, filtered, and linked to executions cheaply. Workflows are pulled in wholesale by `syncWorkflows()` and kept current on a schedule — see [Sync Jobs & Commands](sync-jobs-and-commands.md).

## Workflow Executions

A WorkflowExecution records the outcome of a single run: which workflow, when it started/finished, and how it ended. Every platform's own status vocabulary is normalized into one shared set: `success`, `error`, `running`, `waiting`, `canceled` — so a dashboard or alert never has to special-case "what does 'error' mean on Zapier vs. n8n."

`WorkflowExecutionsPerspective` is the read-oriented, denormalized view of this data for reporting: `status`, `trigger_mode`, `started_at`, `finished_at`, `duration_ms`, `error_message`, `error_node`, `external_execution_id`, plus the workflow name and provider name/type already joined in — built specifically so a UI can render an execution feed without assembling the joins itself.

## API Examples

**List a provider's synced workflows**

```
GET /ipaas/workflows?filter[ipaas_provider_id]=a1b2...-uuid
```

**Deactivate a workflow**

```
POST /ipaas/workflows/{id}/do/deactivate
```

**List recent executions for one workflow, enriched with context**

```
GET /ipaas/workflow-executions-perspective?filter[ipaas_workflow_id]=c3d4...-uuid&sort=-started_at
```

## Related Features

- [Providers & Platforms](providers-and-platforms.md) — a Workflow always belongs to a Provider
- [Sync Jobs & Commands](sync-jobs-and-commands.md) — how Workflows/Executions get pulled in, on schedule or on demand
- [Stats & Health](stats-and-health.md) — the aggregates built on top of execution history
