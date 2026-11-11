# Sales & Marketing Automation

This is why IPAAS exists: to automate the sales & marketing pipeline by connecting the platform to the automation tools — n8n, Make.com, Zapier — that actually run those automations. This page is the honest picture of the split: what IPAAS itself does in code, versus what happens inside the automation platforms it manages.

## The Division of Labor

**IPAAS does not contain sales or marketing logic.** There is no code in this module that references CRM, Marketplace, quotes, opportunities, or campaigns — confirmed by searching the module for any such reference; there are none. What IPAAS actually does is:

- Register a connection to an automation platform (a Provider)
- Pull down the automations that exist there (Workflows) and their run history (WorkflowExecutions)
- Track health and volume so an operator can see, from inside the platform, whether the automations are running and succeeding
- Let those automations be activated/deactivated and inspected without leaving the platform's own UI/API

**The actual sales/marketing automation logic lives inside the n8n workflow, Make scenario, or Zap itself** — built and edited on that platform, not in this codebase. A typical example: an n8n workflow that watches for a new Shopify order (via the Marketplace module's synced data or directly via Shopify), enriches the customer, creates or updates a CRM lead, and kicks off an email sequence — all defined as nodes in n8n, none of it as PHP in this repository.

## What This Means in Practice

- **Building** a new sales/marketing automation happens in n8n/Make/Zapier's own editor, using whatever platform-native connectors and logic those tools provide — Marketplace and CRM data can be reached from there via their public APIs, the same way any other external system would call in
- **Registering** that automation with the platform (so it shows up here, gets synced, and gets monitored) is what IPAAS's Providers/Workflows layer is for — see [Providers & Platforms](providers-and-platforms.md)
- **Watching** whether it's actually working — run counts, error rates, last-execution status — is what the [Stats & Health](stats-and-health.md) layer is for, without needing to go check the automation platform's own dashboard
- **Reacting quickly** to a workflow starting/failing in near-real-time (rather than waiting for the next scheduled sync) is what the [n8n execution hooks](sync-jobs-and-commands.md#real-time-n8n-execution-hooks) provide

## Why an Abstraction Layer, Not a Direct Integration

Sales and marketing teams don't all standardize on the same automation tool — some workflows are easier in Make, some in Zapier, some need n8n's self-hosted flexibility (e.g. to reach an internal-only API). Rather than build and maintain three separate one-off integrations against CRM/Marketplace, IPAAS gives every platform the same `WapInterface` contract (`testConnection`, `listWorkflows`, `syncExecutions`, etc. — see [Workflows & Executions](workflows-and-executions.md)), so a new automation platform can be onboarded once and every existing dashboard/report/health check keeps working unchanged.

## Related Features

- [Providers & Platforms](providers-and-platforms.md) — connecting the n8n/Make/Zapier account behind a given automation
- [Workflows & Executions](workflows-and-executions.md) — the unified model of "an automation" and "a run of it"
- [Stats & Health](stats-and-health.md) — proving the automation is actually working
