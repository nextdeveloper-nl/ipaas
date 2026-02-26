<?php

namespace NextDeveloper\IPAAS\Integrations\Contracts;

/**
 * WapInterface — Workflow Automation Provider contract.
 *
 * Every provider integration (N8N, Make, Zapier, etc.) must implement this
 * interface. The methods are intentionally minimal and provider-agnostic: they
 * accept and return plain arrays so that callers are not coupled to any
 * provider SDK or response object.
 *
 * All methods throw \RuntimeException (or a subclass) on unrecoverable errors
 * such as authentication failures or unexpected API responses.
 */
interface WapInterface
{
    // -------------------------------------------------------------------------
    // Connection
    // -------------------------------------------------------------------------

    /**
     * Verify that the provider API is reachable and the credentials are valid.
     *
     * @return bool  true if the connection succeeds, false if the provider is
     *               unreachable but the error is non-fatal (e.g. timeout).
     * @throws \RuntimeException  on authentication failure.
     */
    public function testConnection(): bool;

    // -------------------------------------------------------------------------
    // Workflows
    // -------------------------------------------------------------------------

    /**
     * Return all workflows available in the provider account.
     *
     * Each element in the returned array must contain at least:
     *   - 'external_id'   (string)  provider-assigned workflow identifier
     *   - 'name'          (string)  human-readable workflow name
     *   - 'status'        (string)  e.g. 'active' | 'inactive'
     *   - 'trigger_type'  (string)  e.g. 'webhook' | 'schedule' | 'manual'
     *
     * @return array<int, array<string, mixed>>
     */
    public function listWorkflows(): array;

    /**
     * Fetch a single workflow by its provider-assigned ID.
     *
     * @param  string $externalId  Provider workflow ID.
     * @return array<string, mixed>
     * @throws \RuntimeException  if the workflow does not exist.
     */
    public function getWorkflow(string $externalId): array;

    /**
     * Activate (enable) a workflow so it can receive triggers.
     *
     * @param  string $externalId  Provider workflow ID.
     * @return bool  true on success.
     * @throws \RuntimeException  on failure.
     */
    public function activateWorkflow(string $externalId): bool;

    /**
     * Deactivate (pause) a workflow so it stops receiving triggers.
     *
     * @param  string $externalId  Provider workflow ID.
     * @return bool  true on success.
     * @throws \RuntimeException  on failure.
     */
    public function deactivateWorkflow(string $externalId): bool;

    // -------------------------------------------------------------------------
    // Executions
    // -------------------------------------------------------------------------

    /**
     * Fetch a single execution by its provider-assigned ID.
     *
     * The returned array must contain at least:
     *   - 'external_id'    (string)       provider-assigned execution identifier
     *   - 'workflow_id'    (string)       provider workflow ID this belongs to
     *   - 'status'         (string)       'success' | 'error' | 'running' | 'waiting' | 'canceled'
     *   - 'trigger_mode'   (string)       'webhook' | 'schedule' | 'manual' | 'trigger' | 'retry'
     *   - 'started_at'     (string|null)  ISO-8601 datetime
     *   - 'finished_at'    (string|null)  ISO-8601 datetime
     *   - 'error_message'  (string|null)
     *   - 'error_node'     (string|null)  name of the node that failed
     *
     * @param  string $executionId  Provider execution ID.
     * @return array<string, mixed>
     * @throws \RuntimeException  if the execution does not exist.
     */
    public function getExecution(string $executionId): array;

    /**
     * List executions, optionally filtered.
     *
     * Supported filter keys (all optional):
     *   - 'workflow_id'  (string)  restrict to a single workflow
     *   - 'status'       (string)  e.g. 'error'
     *   - 'limit'        (int)     max number of results (provider default if omitted)
     *   - 'cursor'       (string)  pagination cursor from a previous call
     *
     * @param  array<string, mixed> $filters
     * @return array{data: array<int, array<string, mixed>>, next_cursor: string|null}
     */
    public function listExecutions(array $filters = []): array;

    /**
     * Delete an execution record from the provider (does not affect our DB).
     *
     * @param  string $executionId  Provider execution ID.
     * @return bool  true on success.
     * @throws \RuntimeException  on failure.
     */
    public function deleteExecution(string $executionId): bool;

    // -------------------------------------------------------------------------
    // Sync helpers
    // -------------------------------------------------------------------------

    /**
     * Pull all workflows from the provider and upsert them into
     * ipaas_workflows, associating them with the provider's DB record.
     *
     * Implementations should use WorkflowsService::create / update internally.
     *
     * @return int  number of workflows upserted.
     */
    public function syncWorkflows(): int;

    /**
     * Pull executions from the provider since the given datetime and upsert
     * them into ipaas_workflow_executions.
     *
     * @param  \DateTimeInterface|null $since  defaults to 24 hours ago.
     * @return int  number of executions upserted.
     */
    public function syncExecutions(?\DateTimeInterface $since = null): int;
}
