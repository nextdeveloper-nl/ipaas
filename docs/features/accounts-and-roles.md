# Accounts & Roles

IPAAS's own Accounts layer is the same thin opt-in pattern as Marketplace's, plus two roles that shape who can see and touch what.

## Key Capabilities

- Turn IPAAS on/off per IAM account with a single flag
- Suspend IPAAS access safely even for an account that never opted in
- Give tenant users full control over their own account's providers/workflows/executions
- Give internal sales/support staff read visibility and account-setting control across tenants they manage, without workflow-level access

## IPAAS Accounts

An `ipaas_accounts` row is minimal: `iam_account_id`, `is_service_enabled`, and `limits` (a JSON field for plan/quota limits not present on the Marketplace equivalent). `EnableService`/`DisableService` flip `is_service_enabled` via `updateQuietly()` and clear the relevant cache — the identical pattern used in Marketplace's Accounts. `AccountsService::suspend()`/`suspendWithIamAccount()` provide the same no-op-safe suspension for an account that may not have an IPAAS row at all.

## Roles

**`IntegrationPlatformUser`** (level 150) — the regular tenant user. Scoped strictly to their own `iam_account_id`. Full CRUD on Providers, Workflows, and Workflow Executions, plus workflow-daily-stats; read-only (with create/update) on Accounts; read-only on all stats and perspective views. This is the role a customer using IPAAS to manage their own automations has.

**`IpaasSalesPerson`** (level 140) — not a tenant role at all. Per its own description: "can read all iPaaS records across tenants and update iPaaS account settings to support customers." Its permissions are narrow and deliberate — only `ipaas_accounts:read/create/update`, nothing on providers/workflows/executions directly. Visibility is scoped through `crm_account_managers`: a sales person only sees accounts they're the assigned CRM account manager for (with a full bypass for `sales-admin`/`sales-manager-admin`), and `checkUpdatePolicy` re-confirms that assignment before allowing a change; delete is unconditionally denied. In other words, this role exists so a sales rep working a customer relationship can check/adjust whether IPAAS is turned on for that account — it's a support/enablement role tied to CRM account ownership, not a workflow-visibility or reporting role.

## API Examples

**Enable IPAAS for an account**

```
POST /ipaas/accounts
```
```json
{
  "iam_account_id": "9d4e...-uuid",
  "is_service_enabled": true
}
```

**Disable IPAAS for an account**

```
POST /ipaas/accounts/{id}/do/disable-service
```

## Related Features

- [Providers & Platforms](providers-and-platforms.md) — what an `IntegrationPlatformUser` manages within their own account
- [Sales & Marketing Automation](sales-and-marketing-automation.md) — the bigger picture this access model supports
