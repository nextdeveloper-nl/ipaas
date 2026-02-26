<?php

namespace NextDeveloper\IPAAS\Integrations\N8N;

use NextDeveloper\IPAAS\Integrations\Contracts\WapInterface;

/**
 * N8NInterface
 *
 * Extends the generic WapInterface with N8N-specific API operations that are
 * not part of the provider-agnostic contract.
 *
 * All methods follow the same conventions as WapInterface:
 *   - Accept and return plain arrays (no N8N SDK coupling).
 *   - Throw \RuntimeException on unrecoverable errors.
 *   - Return normalised keys (snake_case, internal vocabulary).
 *
 * API reference: https://docs.n8n.io/api/api-reference/
 */
interface N8NInterface extends WapInterface
{
    // -------------------------------------------------------------------------
    // Workflow management (full CRUD + execution + tag assignment)
    // -------------------------------------------------------------------------

    /**
     * Create a new workflow in N8N.
     *
     * Required keys in $data:
     *   - 'name'         (string)  human-readable workflow name
     *   - 'nodes'        (array)   array of node objects
     *   - 'connections'  (array)   node connection map
     *
     * Optional keys:
     *   - 'settings'     (array)   workflow settings
     *   - 'staticData'   (array)   static data for the workflow
     *   - 'tags'         (array)   array of tag IDs to assign
     *
     * @param  array<string, mixed> $data
     * @return array<string, mixed>  normalised workflow
     * @throws \RuntimeException
     */
    public function createWorkflow(array $data): array;

    /**
     * Fully replace a workflow's definition in N8N (PUT semantics).
     *
     * $data should contain the complete workflow body (same shape as createWorkflow).
     * Fields omitted from $data will be reset to their defaults by N8N.
     *
     * @param  string               $externalId  Provider workflow ID.
     * @param  array<string, mixed> $data
     * @return array<string, mixed>  normalised workflow
     * @throws \RuntimeException
     */
    public function updateWorkflow(string $externalId, array $data): array;

    /**
     * Permanently delete a workflow from N8N.
     *
     * This removes the workflow from the N8N instance. The corresponding
     * ipaas_workflows record is NOT deleted; callers must handle that separately.
     *
     * @param  string $externalId  Provider workflow ID.
     * @return bool  true on success.
     * @throws \RuntimeException
     */
    public function deleteWorkflow(string $externalId): bool;

    /**
     * Manually trigger a workflow execution via the N8N REST API.
     *
     * The workflow must have a "When executed by another workflow" trigger node,
     * or $waitTillDone must be false (fire-and-forget mode).
     *
     * Optional keys in $inputData:
     *   - 'runData'          (array)   manual run data per node
     *   - 'pinData'          (array)   pinned data per node
     *   - 'startNodes'       (array)   node names to start from
     *   - 'destinationNode'  (string)  node name to run up to
     *
     * @param  string               $externalId    Provider workflow ID.
     * @param  array<string, mixed> $inputData      Optional input payload.
     * @param  bool                 $waitTillDone   Whether to wait for the run to complete.
     * @return array<string, mixed>  contains at least 'execution_id' (string)
     * @throws \RuntimeException
     */
    public function executeWorkflow(string $externalId, array $inputData = [], bool $waitTillDone = false): array;

    /**
     * Return the tags currently assigned to a workflow.
     *
     * Each element contains: 'external_id', 'name'.
     *
     * @param  string $externalId  Provider workflow ID.
     * @return array<int, array<string, mixed>>
     * @throws \RuntimeException
     */
    public function getWorkflowTags(string $externalId): array;

    /**
     * Replace all tags on a workflow with the given tag IDs.
     *
     * Passing an empty array removes all tags from the workflow.
     *
     * @param  string   $externalId  Provider workflow ID.
     * @param  string[] $tagIds      Array of N8N tag IDs to assign.
     * @return array<int, array<string, mixed>>  normalised tags after update
     * @throws \RuntimeException
     */
    public function updateWorkflowTags(string $externalId, array $tagIds): array;

    // -------------------------------------------------------------------------
    // Execution management (N8N-specific additions)
    // -------------------------------------------------------------------------

    /**
     * Retry a failed execution.
     *
     * @param  string $executionId   Provider execution ID.
     * @param  bool   $loadWorkflow  Whether to re-load the latest workflow definition.
     * @return array<string, mixed>  normalised execution record of the new retry run
     * @throws \RuntimeException
     */
    public function retryExecution(string $executionId, bool $loadWorkflow = false): array;

    // -------------------------------------------------------------------------
    // Credential management
    // -------------------------------------------------------------------------

    /**
     * List all credentials in the N8N instance.
     *
     * Supported filter keys (all optional):
     *   - 'limit'        (int)     max results per page
     *   - 'cursor'       (string)  pagination cursor
     *   - 'include_data' (bool)    whether to include decrypted credential data
     *
     * Each element contains at least: 'external_id', 'name', 'type', 'created_at'.
     *
     * @param  array<string, mixed> $filters
     * @return array{data: array<int, array<string, mixed>>, next_cursor: string|null}
     */
    public function listCredentials(array $filters = []): array;

    /**
     * Fetch a single credential by its N8N ID.
     *
     * @param  string $externalId  N8N credential ID.
     * @return array<string, mixed>
     * @throws \RuntimeException  if the credential does not exist.
     */
    public function getCredential(string $externalId): array;

    /**
     * Create a new credential in N8N.
     *
     * @param  string               $type  N8N credential type (e.g. 'httpBasicAuth').
     * @param  string               $name  Human-readable name.
     * @param  array<string, mixed> $data  Credential-specific fields (see getCredentialSchema).
     * @return array<string, mixed>  normalised credential
     * @throws \RuntimeException
     */
    public function createCredential(string $type, string $name, array $data): array;

    /**
     * Delete a credential from N8N.
     *
     * @param  string $externalId  N8N credential ID.
     * @return bool  true on success.
     * @throws \RuntimeException
     */
    public function deleteCredential(string $externalId): bool;

    /**
     * Fetch the JSON schema for a given credential type.
     *
     * Useful for dynamically rendering credential creation forms.
     *
     * @param  string $credentialTypeName  e.g. 'httpBasicAuth', 'slackApi'.
     * @return array<string, mixed>  JSON schema object
     * @throws \RuntimeException
     */
    public function getCredentialSchema(string $credentialTypeName): array;

    // -------------------------------------------------------------------------
    // Tag management
    // -------------------------------------------------------------------------

    /**
     * List all tags defined in the N8N instance.
     *
     * Supported filter keys (all optional):
     *   - 'limit'   (int)     max results per page
     *   - 'cursor'  (string)  pagination cursor
     *
     * Each element contains: 'external_id', 'name', 'created_at', 'updated_at'.
     *
     * @param  array<string, mixed> $filters
     * @return array{data: array<int, array<string, mixed>>, next_cursor: string|null}
     */
    public function listTags(array $filters = []): array;

    /**
     * Create a new tag.
     *
     * @param  string $name
     * @return array<string, mixed>  normalised tag
     * @throws \RuntimeException
     */
    public function createTag(string $name): array;

    /**
     * Rename an existing tag.
     *
     * @param  string $externalId  N8N tag ID.
     * @param  string $name        New tag name.
     * @return array<string, mixed>  normalised tag
     * @throws \RuntimeException
     */
    public function updateTag(string $externalId, string $name): array;

    /**
     * Delete a tag. The tag is removed from all workflows it was assigned to.
     *
     * @param  string $externalId  N8N tag ID.
     * @return bool  true on success.
     * @throws \RuntimeException
     */
    public function deleteTag(string $externalId): bool;

    // -------------------------------------------------------------------------
    // Variable management
    // -------------------------------------------------------------------------

    /**
     * List all variables defined in the N8N instance.
     *
     * Each element contains: 'external_id', 'key', 'value', 'type'.
     *
     * @return array{data: array<int, array<string, mixed>>, next_cursor: string|null}
     */
    public function listVariables(): array;

    /**
     * Create a new instance-level variable.
     *
     * @param  string $key    Variable key (must be unique).
     * @param  string $value  Variable value (always stored as string internally).
     * @param  string $type   'string' (default) | 'number' | 'boolean' | 'object' | 'array'.
     * @return array<string, mixed>  normalised variable
     * @throws \RuntimeException
     */
    public function createVariable(string $key, string $value, string $type = 'string'): array;

    /**
     * Delete a variable by its N8N ID.
     *
     * @param  string $externalId  N8N variable ID.
     * @return bool  true on success.
     * @throws \RuntimeException
     */
    public function deleteVariable(string $externalId): bool;

    // -------------------------------------------------------------------------
    // User management
    // -------------------------------------------------------------------------

    /**
     * List all users on the N8N instance (owner-only operation).
     *
     * Supported filter keys (all optional):
     *   - 'limit'        (int)   max results
     *   - 'cursor'       (string) pagination cursor
     *   - 'include_role' (bool)  include role information
     *
     * Each element contains: 'external_id', 'email', 'first_name', 'last_name',
     * 'role', 'is_pending', 'created_at'.
     *
     * @param  array<string, mixed> $filters
     * @return array{data: array<int, array<string, mixed>>, next_cursor: string|null}
     */
    public function listUsers(array $filters = []): array;

    /**
     * Invite one or more users to the N8N instance.
     *
     * Each user entry must contain:
     *   - 'email'  (string)  user email address
     *   - 'role'   (string)  'global:admin' | 'global:member'
     *
     * @param  array<int, array{email: string, role: string}> $users
     * @return array<int, array<string, mixed>>  normalised user records
     * @throws \RuntimeException
     */
    public function inviteUsers(array $users): array;

    /**
     * Delete a user from the N8N instance (owner-only operation).
     *
     * @param  string $externalId  N8N user ID.
     * @return bool  true on success.
     * @throws \RuntimeException
     */
    public function deleteUser(string $externalId): bool;

    /**
     * Change a user's global role.
     *
     * @param  string $externalId   N8N user ID.
     * @param  string $newRoleName  'global:admin' | 'global:member'.
     * @return bool  true on success.
     * @throws \RuntimeException
     */
    public function changeUserRole(string $externalId, string $newRoleName): bool;
}
