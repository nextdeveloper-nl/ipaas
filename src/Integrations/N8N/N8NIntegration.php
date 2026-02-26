<?php

namespace NextDeveloper\IPAAS\Integrations\N8N;

use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Database\Models\WorkflowExecutions;
use NextDeveloper\IPAAS\Database\Models\Workflows;
use NextDeveloper\IPAAS\Integrations\AbstractWapIntegration;

/**
 * N8NIntegration
 *
 * Implements N8NInterface for self-hosted and cloud N8N instances using
 * the N8N public REST API (v1).
 *
 * API reference: https://docs.n8n.io/api/api-reference/
 *
 * Credentials are read from the Providers model:
 *   - $provider->region              →  N8N base URL  (e.g. https://n8n.example.com)
 *   - $provider->external_account_id →  N8N API key   (Settings → API → Create API key)
 *
 * N8N API authentication uses the X-N8N-API-KEY header on every request.
 */
class N8NIntegration extends AbstractWapIntegration implements N8NInterface
{
    /**
     * Base path for all N8N API v1 endpoints.
     */
    private const API_PREFIX = '/api/v1';

    /**
     * N8N execution status values mapped to our internal status vocabulary.
     */
    private const STATUS_MAP = [
        'success'  => 'success',
        'error'    => 'error',
        'running'  => 'running',
        'waiting'  => 'waiting',
        'canceled' => 'canceled',
        'unknown'  => 'error',
    ];

    /**
     * N8N execution mode values mapped to our trigger_mode vocabulary.
     */
    private const MODE_MAP = [
        'webhook'    => 'webhook',
        'trigger'    => 'trigger',
        'manual'     => 'manual',
        'retry'      => 'retry',
        'integrated' => 'trigger',
        'cli'        => 'manual',
        'internal'   => 'manual',
        'evaluation' => 'manual',
    ];

    /**
     * N8N global role names recognised by the API.
     */
    private const VALID_ROLES = ['global:admin', 'global:member'];

    // -------------------------------------------------------------------------
    // Auth
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * N8N uses the X-N8N-API-KEY header for authentication.
     */
    protected function defaultHeaders(): array
    {
        return array_merge(parent::defaultHeaders(), [
            'X-N8N-API-KEY' => $this->apiKey,
        ]);
    }

    // -------------------------------------------------------------------------
    // Connection
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v1/workflows?limit=1 as a lightweight connectivity probe.
     */
    public function testConnection(): bool
    {
        try {
            $this->get(self::API_PREFIX . '/workflows', ['limit' => 1]);
            return true;
        } catch (\RuntimeException $e) {
            // 401 / 403 → auth failure, re-throw so the caller knows credentials are wrong
            if (in_array($e->getCode(), [401, 403], true)) {
                throw $e;
            }

            // Network / timeout → non-fatal
            return false;
        }
    }

    // -------------------------------------------------------------------------
    // Workflows — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Paginates through all N8N workflows using the cursor-based API.
     */
    public function listWorkflows(): array
    {
        $results = [];
        $cursor  = null;

        do {
            $query = ['limit' => 100];

            if ($cursor !== null) {
                $query['cursor'] = $cursor;
            }

            $response = $this->get(self::API_PREFIX . '/workflows', $query);
            $page     = $response['data'] ?? $response;

            foreach ((array) $page as $workflow) {
                $results[] = $this->normaliseWorkflow($workflow);
            }

            $cursor = $response['nextCursor'] ?? null;
        } while ($cursor !== null);

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow(string $externalId): array
    {
        $response = $this->get(self::API_PREFIX . '/workflows/' . $externalId);

        return $this->normaliseWorkflow($response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/workflows/{id}/activate
     */
    public function activateWorkflow(string $externalId): bool
    {
        $this->post(self::API_PREFIX . '/workflows/' . $externalId . '/activate');

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/workflows/{id}/deactivate
     */
    public function deactivateWorkflow(string $externalId): bool
    {
        $this->post(self::API_PREFIX . '/workflows/' . $externalId . '/deactivate');

        return true;
    }

    // -------------------------------------------------------------------------
    // Workflows — N8NInterface (full CRUD + execute + tag assignment)
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/workflows
     */
    public function createWorkflow(array $data): array
    {
        $body = [
            'name'        => $data['name'],
            'nodes'       => $data['nodes'] ?? [],
            'connections' => $data['connections'] ?? [],
            'settings'    => $data['settings'] ?? [],
        ];

        if (isset($data['staticData'])) {
            $body['staticData'] = $data['staticData'];
        }

        if (!empty($data['tags'])) {
            $body['tags'] = array_map(
                fn($id) => ['id' => (string) $id],
                $data['tags']
            );
        }

        $response = $this->post(self::API_PREFIX . '/workflows', $body);

        return $this->normaliseWorkflow($response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls PUT /api/v1/workflows/{id}
     */
    public function updateWorkflow(string $externalId, array $data): array
    {
        $body = [
            'name'        => $data['name'],
            'nodes'       => $data['nodes'] ?? [],
            'connections' => $data['connections'] ?? [],
            'settings'    => $data['settings'] ?? [],
        ];

        if (isset($data['staticData'])) {
            $body['staticData'] = $data['staticData'];
        }

        $response = $this->put(self::API_PREFIX . '/workflows/' . $externalId, $body);

        return $this->normaliseWorkflow($response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls DELETE /api/v1/workflows/{id}
     */
    public function deleteWorkflow(string $externalId): bool
    {
        $this->delete(self::API_PREFIX . '/workflows/' . $externalId);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/workflows/{id}/run
     *
     * Note: the workflow must have a supported trigger node for REST execution.
     * Returns an array containing 'execution_id' on fire-and-forget mode, or
     * the full normalised execution when $waitTillDone is true.
     */
    public function executeWorkflow(string $externalId, array $inputData = [], bool $waitTillDone = false): array
    {
        $body = [];

        if (!empty($inputData['runData'])) {
            $body['runData'] = $inputData['runData'];
        }

        if (!empty($inputData['pinData'])) {
            $body['pinData'] = $inputData['pinData'];
        }

        if (!empty($inputData['startNodes'])) {
            $body['startNodes'] = $inputData['startNodes'];
        }

        if (!empty($inputData['destinationNode'])) {
            $body['destinationNode'] = $inputData['destinationNode'];
        }

        $response = $this->post(
            self::API_PREFIX . '/workflows/' . $externalId . '/run',
            $body
        );

        // N8N returns { "executionId": "..." } for async runs
        if (isset($response['executionId'])) {
            $result = ['execution_id' => (string) $response['executionId']];

            if ($waitTillDone) {
                $result = array_merge(
                    $result,
                    $this->getExecution((string) $response['executionId'])
                );
            }

            return $result;
        }

        // Inline execution result
        return array_merge(
            ['execution_id' => null],
            $response
        );
    }

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v1/workflows/{id}/tags
     */
    public function getWorkflowTags(string $externalId): array
    {
        $response = $this->get(self::API_PREFIX . '/workflows/' . $externalId . '/tags');
        $items    = is_array($response['data'] ?? null) ? $response['data'] : $response;

        return array_map(fn($tag) => $this->normaliseTag($tag), (array) $items);
    }

    /**
     * {@inheritdoc}
     *
     * Calls PUT /api/v1/workflows/{id}/tags
     *
     * N8N expects: [{ "id": "tag-id" }, ...]
     */
    public function updateWorkflowTags(string $externalId, array $tagIds): array
    {
        $body = array_map(fn($id) => ['id' => (string) $id], $tagIds);

        $response = $this->put(
            self::API_PREFIX . '/workflows/' . $externalId . '/tags',
            $body
        );

        $items = is_array($response['data'] ?? null) ? $response['data'] : $response;

        return array_map(fn($tag) => $this->normaliseTag($tag), (array) $items);
    }

    // -------------------------------------------------------------------------
    // Executions — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function getExecution(string $executionId): array
    {
        $response = $this->get(self::API_PREFIX . '/executions/' . $executionId);

        return $this->normaliseExecution($response);
    }

    /**
     * {@inheritdoc}
     *
     * Supported filter keys: workflow_id, status, limit, cursor.
     */
    public function listExecutions(array $filters = []): array
    {
        $query = [];

        if (!empty($filters['workflow_id'])) {
            $query['workflowId'] = $filters['workflow_id'];
        }

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        if (!empty($filters['limit'])) {
            $query['limit'] = (int) $filters['limit'];
        }

        if (!empty($filters['cursor'])) {
            $query['cursor'] = $filters['cursor'];
        }

        $response = $this->get(self::API_PREFIX . '/executions', $query);
        $items    = $response['data'] ?? $response;

        return [
            'data'        => array_map(fn($e) => $this->normaliseExecution($e), (array) $items),
            'next_cursor' => $response['nextCursor'] ?? null,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Calls DELETE /api/v1/executions/{id}
     */
    public function deleteExecution(string $executionId): bool
    {
        $this->delete(self::API_PREFIX . '/executions/' . $executionId);

        return true;
    }

    // -------------------------------------------------------------------------
    // Executions — N8NInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/executions/{id}/retry
     */
    public function retryExecution(string $executionId, bool $loadWorkflow = false): array
    {
        $response = $this->post(
            self::API_PREFIX . '/executions/' . $executionId . '/retry',
            ['loadWorkflow' => $loadWorkflow]
        );

        return $this->normaliseExecution($response);
    }

    // -------------------------------------------------------------------------
    // Credentials — N8NInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v1/credentials
     */
    public function listCredentials(array $filters = []): array
    {
        $query = [];

        if (!empty($filters['limit'])) {
            $query['limit'] = (int) $filters['limit'];
        }

        if (!empty($filters['cursor'])) {
            $query['cursor'] = $filters['cursor'];
        }

        if (isset($filters['include_data'])) {
            $query['includeData'] = (bool) $filters['include_data'];
        }

        $response = $this->get(self::API_PREFIX . '/credentials', $query);
        $items    = $response['data'] ?? $response;

        return [
            'data'        => array_map(fn($c) => $this->normaliseCredential($c), (array) $items),
            'next_cursor' => $response['nextCursor'] ?? null,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v1/credentials/{id}
     */
    public function getCredential(string $externalId): array
    {
        $response = $this->get(self::API_PREFIX . '/credentials/' . $externalId);

        return $this->normaliseCredential($response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/credentials
     */
    public function createCredential(string $type, string $name, array $data): array
    {
        $response = $this->post(self::API_PREFIX . '/credentials', [
            'name' => $name,
            'type' => $type,
            'data' => $data,
        ]);

        return $this->normaliseCredential($response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls DELETE /api/v1/credentials/{id}
     */
    public function deleteCredential(string $externalId): bool
    {
        $this->delete(self::API_PREFIX . '/credentials/' . $externalId);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v1/credentials/schema/{credentialTypeName}
     */
    public function getCredentialSchema(string $credentialTypeName): array
    {
        return $this->get(self::API_PREFIX . '/credentials/schema/' . $credentialTypeName);
    }

    // -------------------------------------------------------------------------
    // Tags — N8NInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v1/tags
     */
    public function listTags(array $filters = []): array
    {
        $query = [];

        if (!empty($filters['limit'])) {
            $query['limit'] = (int) $filters['limit'];
        }

        if (!empty($filters['cursor'])) {
            $query['cursor'] = $filters['cursor'];
        }

        $response = $this->get(self::API_PREFIX . '/tags', $query);
        $items    = $response['data'] ?? $response;

        return [
            'data'        => array_map(fn($t) => $this->normaliseTag($t), (array) $items),
            'next_cursor' => $response['nextCursor'] ?? null,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/tags
     */
    public function createTag(string $name): array
    {
        $response = $this->post(self::API_PREFIX . '/tags', ['name' => $name]);

        return $this->normaliseTag($response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls PUT /api/v1/tags/{id}
     */
    public function updateTag(string $externalId, string $name): array
    {
        $response = $this->put(self::API_PREFIX . '/tags/' . $externalId, ['name' => $name]);

        return $this->normaliseTag($response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls DELETE /api/v1/tags/{id}
     */
    public function deleteTag(string $externalId): bool
    {
        $this->delete(self::API_PREFIX . '/tags/' . $externalId);

        return true;
    }

    // -------------------------------------------------------------------------
    // Variables — N8NInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v1/variables
     */
    public function listVariables(): array
    {
        $response = $this->get(self::API_PREFIX . '/variables');
        $items    = $response['data'] ?? $response;

        return [
            'data'        => array_map(fn($v) => $this->normaliseVariable($v), (array) $items),
            'next_cursor' => $response['nextCursor'] ?? null,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/variables
     */
    public function createVariable(string $key, string $value, string $type = 'string'): array
    {
        $response = $this->post(self::API_PREFIX . '/variables', [
            'key'   => $key,
            'value' => $value,
            'type'  => $type,
        ]);

        return $this->normaliseVariable($response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls DELETE /api/v1/variables/{id}
     */
    public function deleteVariable(string $externalId): bool
    {
        $this->delete(self::API_PREFIX . '/variables/' . $externalId);

        return true;
    }

    // -------------------------------------------------------------------------
    // Users — N8NInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v1/users
     */
    public function listUsers(array $filters = []): array
    {
        $query = [];

        if (!empty($filters['limit'])) {
            $query['limit'] = (int) $filters['limit'];
        }

        if (!empty($filters['cursor'])) {
            $query['cursor'] = $filters['cursor'];
        }

        if (isset($filters['include_role'])) {
            $query['includeRole'] = (bool) $filters['include_role'];
        }

        $response = $this->get(self::API_PREFIX . '/users', $query);
        $items    = $response['data'] ?? $response;

        return [
            'data'        => array_map(fn($u) => $this->normaliseUser($u), (array) $items),
            'next_cursor' => $response['nextCursor'] ?? null,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v1/users
     *
     * Each entry in $users: ['email' => '...', 'role' => 'global:admin' | 'global:member']
     */
    public function inviteUsers(array $users): array
    {
        foreach ($users as $user) {
            if (!in_array($user['role'] ?? '', self::VALID_ROLES, true)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid role "%s". Valid roles: %s',
                        $user['role'] ?? '',
                        implode(', ', self::VALID_ROLES)
                    )
                );
            }
        }

        $response = $this->post(self::API_PREFIX . '/users', $users);
        $items    = is_array($response['data'] ?? null) ? $response['data'] : $response;

        return array_map(fn($u) => $this->normaliseUser($u), (array) $items);
    }

    /**
     * {@inheritdoc}
     *
     * Calls DELETE /api/v1/users/{id}
     */
    public function deleteUser(string $externalId): bool
    {
        $this->delete(self::API_PREFIX . '/users/' . $externalId);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Calls PATCH /api/v1/users/{id}/role
     */
    public function changeUserRole(string $externalId, string $newRoleName): bool
    {
        if (!in_array($newRoleName, self::VALID_ROLES, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid role "%s". Valid roles: %s',
                    $newRoleName,
                    implode(', ', self::VALID_ROLES)
                )
            );
        }

        $this->patch(
            self::API_PREFIX . '/users/' . $externalId . '/role',
            ['newRoleName' => $newRoleName]
        );

        return true;
    }

    // -------------------------------------------------------------------------
    // Sync — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Fetches all workflows from N8N and upserts them into ipaas_workflows,
     * keyed by (external_workflow_id, ipaas_provider_id).
     */
    public function syncWorkflows(): int
    {
        $workflows = $this->listWorkflows();
        $count     = 0;

        foreach ($workflows as $wf) {
            Workflows::updateOrCreate(
                [
                    'external_workflow_id' => $wf['external_id'],
                    'ipaas_provider_id'    => $this->provider->id,
                ],
                [
                    'name'            => $wf['name'],
                    'description'     => $wf['description'] ?? null,
                    'trigger_type'    => $wf['trigger_type'],
                    'status'          => $wf['status'],
                    'iam_account_id'  => $this->provider->iam_account_id,
                    'iam_user_id'     => $this->provider->iam_user_id,
                    'last_synched_at' => now(),
                ]
            );

            $count++;
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     *
     * Fetches executions since $since (defaults to 24 hours ago) and upserts
     * them into ipaas_workflow_executions.
     */
    public function syncExecutions(?\DateTimeInterface $since = null): int
    {
        $since  = $since ?? now()->subDay();
        $cursor = null;
        $count  = 0;

        do {
            $result = $this->listExecutions([
                'limit'  => 100,
                'cursor' => $cursor,
            ]);

            foreach ($result['data'] as $execution) {
                // Stop pagination when we reach executions older than our window
                if ($execution['started_at'] !== null &&
                    strtotime($execution['started_at']) < $since->getTimestamp()
                ) {
                    $cursor = null;
                    break;
                }

                $workflow = Workflows::where('external_workflow_id', $execution['workflow_id'])
                    ->where('ipaas_provider_id', $this->provider->id)
                    ->first();

                if (!$workflow) {
                    continue;
                }

                WorkflowExecutions::updateOrCreate(
                    [
                        'external_execution_id' => $execution['external_id'],
                        'ipaas_provider_id'     => $this->provider->id,
                    ],
                    [
                        'ipaas_workflow_id' => $workflow->id,
                        'iam_account_id'    => $this->provider->iam_account_id,
                        'status'            => $execution['status'],
                        'trigger_mode'      => $execution['trigger_mode'],
                        'started_at'        => $execution['started_at'],
                        'finished_at'       => $execution['finished_at'],
                        'error_message'     => $execution['error_message'],
                        'error_node'        => $execution['error_node'],
                    ]
                );

                $count++;
            }

            $cursor = $result['next_cursor'];
        } while ($cursor !== null);

        return $count;
    }

    // -------------------------------------------------------------------------
    // Normalisation helpers
    // -------------------------------------------------------------------------

    /**
     * Map a raw N8N workflow API response to our internal array shape.
     *
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normaliseWorkflow(array $raw): array
    {
        $triggerType = $this->resolveTriggerType($raw['nodes'] ?? []);

        return [
            'external_id'  => (string) ($raw['id'] ?? ''),
            'name'         => (string) ($raw['name'] ?? ''),
            'description'  => $raw['description'] ?? null,
            'status'       => ($raw['active'] ?? false) ? 'active' : 'inactive',
            'trigger_type' => $triggerType,
            'nodes'        => $raw['nodes'] ?? [],
            'connections'  => $raw['connections'] ?? [],
            'settings'     => $raw['settings'] ?? [],
            'tags'         => $raw['tags'] ?? [],
            'updated_at'   => $raw['updatedAt'] ?? null,
            'created_at'   => $raw['createdAt'] ?? null,
        ];
    }

    /**
     * Map a raw N8N execution API response to our internal array shape.
     *
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normaliseExecution(array $raw): array
    {
        $status      = self::STATUS_MAP[$raw['status'] ?? 'unknown'] ?? 'error';
        $triggerMode = self::MODE_MAP[$raw['mode'] ?? 'manual'] ?? 'manual';

        $errorMessage = null;
        $errorNode    = null;

        if (isset($raw['data']['resultData']['error'])) {
            $err          = $raw['data']['resultData']['error'];
            $errorMessage = $err['message'] ?? null;
            $errorNode    = $err['node']['name'] ?? null;
        }

        return [
            'external_id'   => (string) ($raw['id'] ?? ''),
            'workflow_id'   => (string) ($raw['workflowId'] ?? ''),
            'status'        => $status,
            'trigger_mode'  => $triggerMode,
            'started_at'    => $raw['startedAt'] ?? null,
            'finished_at'   => $raw['stoppedAt'] ?? null,
            'error_message' => $errorMessage,
            'error_node'    => $errorNode,
        ];
    }

    /**
     * Map a raw N8N credential API response to our internal array shape.
     *
     * Note: N8N redacts sensitive fields in credential data by default.
     *
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normaliseCredential(array $raw): array
    {
        return [
            'external_id' => (string) ($raw['id'] ?? ''),
            'name'        => (string) ($raw['name'] ?? ''),
            'type'        => (string) ($raw['type'] ?? ''),
            'data'        => $raw['data'] ?? [],
            'created_at'  => $raw['createdAt'] ?? null,
            'updated_at'  => $raw['updatedAt'] ?? null,
        ];
    }

    /**
     * Map a raw N8N tag API response to our internal array shape.
     *
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normaliseTag(array $raw): array
    {
        return [
            'external_id' => (string) ($raw['id'] ?? ''),
            'name'        => (string) ($raw['name'] ?? ''),
            'created_at'  => $raw['createdAt'] ?? null,
            'updated_at'  => $raw['updatedAt'] ?? null,
        ];
    }

    /**
     * Map a raw N8N variable API response to our internal array shape.
     *
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normaliseVariable(array $raw): array
    {
        return [
            'external_id' => (string) ($raw['id'] ?? ''),
            'key'         => (string) ($raw['key'] ?? ''),
            'value'       => (string) ($raw['value'] ?? ''),
            'type'        => (string) ($raw['type'] ?? 'string'),
        ];
    }

    /**
     * Map a raw N8N user API response to our internal array shape.
     *
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normaliseUser(array $raw): array
    {
        return [
            'external_id' => (string) ($raw['id'] ?? ''),
            'email'       => (string) ($raw['email'] ?? ''),
            'first_name'  => $raw['firstName'] ?? null,
            'last_name'   => $raw['lastName'] ?? null,
            'role'        => $raw['globalRole']['name'] ?? ($raw['role'] ?? null),
            'is_pending'  => (bool) ($raw['isPending'] ?? false),
            'created_at'  => $raw['createdAt'] ?? null,
        ];
    }

    /**
     * Inspect the workflow's node list to determine the trigger type.
     * N8N trigger nodes follow the naming convention *Trigger (e.g. WebhookTrigger,
     * ScheduleTrigger, ManualTrigger).
     *
     * @param  array<int, array<string, mixed>> $nodes
     * @return string  one of: 'webhook' | 'schedule' | 'manual' | 'trigger'
     */
    private function resolveTriggerType(array $nodes): string
    {
        foreach ($nodes as $node) {
            $type = strtolower($node['type'] ?? '');

            if (str_contains($type, 'webhook'))  return 'webhook';
            if (str_contains($type, 'schedule')) return 'schedule';
            if (str_contains($type, 'manual'))   return 'manual';
            if (str_contains($type, 'trigger'))  return 'trigger';
        }

        return 'manual';
    }
}
