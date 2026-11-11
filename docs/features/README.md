# IPaaS Platform Features

IPAAS ("Integration Platform as a Service") is the abstraction layer that lets the platform manage, sync, and monitor workflow automations across third-party automation platforms — n8n, Make.com, and Zapier — through one consistent API. It exists to make it easy to **automate the sales & marketing pipeline**: rather than every automation being a bespoke, hand-wired integration, sales/marketing workflows are built in n8n/Make/Zapier and IPAAS is what registers, syncs, monitors, and reports on them from inside the platform.

This is the feature documentation for that layer. Each page below explains a feature area in plain language and includes brief API examples for the key operations in that area.

All API paths are relative to the platform's API base URL and prefixed with `/ipaas`. Resources are addressed by UUID and follow the same basic operations: list (`GET`), create (`POST`), show/update/delete (`GET` / `PATCH` / `DELETE` on `/{id}`), and custom actions (`POST /{id}/do/{action}`).

## Feature Areas

| Feature | What it covers |
| --- | --- |
| [Sales & Marketing Automation](sales-and-marketing-automation.md) | **Start here.** Why the module exists, and what's actually in-code versus what lives in the automation platforms themselves |
| [Providers & Platforms](providers-and-platforms.md) | Registering an n8n/Make/Zapier account, credentials, and what each platform supports today |
| [Workflows & Executions](workflows-and-executions.md) | The unified Workflow/WorkflowExecution model and normalized status vocabulary |
| [Sync Jobs & Commands](sync-jobs-and-commands.md) | How workflow/execution data gets pulled in — scheduled jobs, Artisan commands, and real-time n8n execution hooks |
| [Stats & Health](stats-and-health.md) | Per-account and per-workflow rollups, daily aggregates, and cross-provider health dashboards |
| [Accounts & Roles](accounts-and-roles.md) | Enabling IPAAS per tenant, and the two authorization roles |

## How These Pages Are Organized

Each page follows the same structure: a short overview, a list of key capabilities, a deeper explanation of each sub-feature, brief API examples for the most common operations, and links to related feature areas.
