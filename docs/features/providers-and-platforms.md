# Providers & Platforms

A **Provider** is one registered connection to an automation platform account — a specific n8n instance, Make.com team, or Zapier account. Connecting a new platform to the org is a matter of creating a Provider row with the right credentials; everything downstream (sync, stats, health) works the same regardless of which platform it points at.

## Key Capabilities

- Register credentials for n8n (self-hosted or cloud), Make.com, or Zapier under one consistent model
- Verify credentials work before committing to a connection (`testConnection()`)
- Scope every provider, workflow, and execution to the owning account, so no tenant sees another tenant's automations
- Add a new automation platform later without touching any existing dashboard/report code, via a shared `WapInterface` contract

## Supported Platforms

| Platform | Type | Workflows | Executions | Activate / Deactivate |
| --- | --- | --- | --- | --- |
| [n8n](https://n8n.io) | self-hosted / cloud | ✅ | ✅ | ✅ |
| [Make.com](https://make.com) | cloud | ✅ (scenarios) | ✅ | ✅ |
| [Zapier](https://zapier.com) | cloud | ✅ (zaps) | ❌ (not in Zapier's public API) | ❌ (not in Zapier's public API) |

Zapier's gaps aren't a limitation of this module — Zapier simply doesn't expose execution history or activate/deactivate endpoints in its public API, so those columns stay empty for Zapier-backed providers.

## Provider Fields Per Platform

| Field | n8n | Make.com | Zapier |
| --- | --- | --- | --- |
| `region` / `base_url` | Instance URL | Regional API base | `https://api.zapier.com/v2` |
| `external_account_id` | N8N API key | Make.com team ID | — |
| `api_token` | — | Make.com API token | Zapier personal token |
| `provider_type` | `n8n` or `n8n-cloud` | `make` | `zapier` |

Credentials live per Provider row — there's no module-wide env var for any platform's credentials, so connecting a new account is a database/admin operation per tenant, the same pattern as the Marketplace module's Providers.

## The Adapter Contract

Every platform integration (`src/Integrations/<Platform>/`) implements `WapInterface` via `AbstractWapIntegration`: `testConnection()`, `listWorkflows()`/`getWorkflow()`, `activateWorkflow()`/`deactivateWorkflow()`, `listExecutions()`/`getExecution()`/`deleteExecution()`, `syncWorkflows()`, `syncExecutions()`. `WapIntegrationFactory` resolves a Provider's `provider_type` to the right integration class, the same registry pattern as Marketplace's `MarketplaceAdapterFactory`.

## Adding a New Platform

1. Create an integration class in `src/Integrations/<Platform>/` extending `AbstractWapIntegration` and implementing `WapInterface`
2. Create a matching interface in the same directory
3. Register the provider type in `WapIntegrationFactory::make()`
4. Add sync jobs under `src/Jobs/<Platform>/`
5. Add Artisan commands under `src/Console/Commands/`

## API Examples

**Register an n8n connection**

```
POST /ipaas/providers
```
```json
{
  "provider_type": "n8n",
  "base_url": "https://n8n.acme.internal",
  "external_account_id": "n8n-api-key-here"
}
```

**Register a Make.com connection**

```
POST /ipaas/providers
```
```json
{
  "provider_type": "make",
  "region": "eu1.make.com",
  "external_account_id": "team-12345",
  "api_token": "make-api-token"
}
```

## Related Features

- [Workflows & Executions](workflows-and-executions.md) — what gets synced once a Provider is connected
- [Sync Jobs & Commands](sync-jobs-and-commands.md) — how and when that sync actually runs
- [Accounts & Roles](accounts-and-roles.md) — how access to a Provider is scoped and gated
