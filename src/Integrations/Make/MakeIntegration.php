<?php

namespace NextDeveloper\IPAAS\Integrations\Make;

use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Database\Models\WorkflowExecutions;
use NextDeveloper\IPAAS\Database\Models\Workflows;
use NextDeveloper\IPAAS\Integrations\AbstractWapIntegration;

/**
 * MakeIntegration
 *
 * Implements MakeInterface for Make.com using the Make REST API v2.
 *
 * API reference: https://developers.make.com/api-documentation
 *
 * Credentials are read from the Providers model:
 *   - $provider->base_url             → Regional API base (e.g. https://eu1.make.com)
 *   - $provider->api_token            → Make.com API token
 *   - $provider->external_account_id  → Make.com team ID (required for listing scenarios)
 *
 * Make.com uses "scenarios" as the equivalent of workflows.
 * Authentication uses the "Authorization: Token {token}" header.
 */
class MakeIntegration extends AbstractWapIntegration implements MakeInterface
{
    /**
     * Base path for all Make.com API v2 endpoints.
     */
    private const API_PREFIX = '/api/v2';

    /**
     * Map Make.com log status codes to our internal status vocabulary.
     * 1 = success, 2 = warning (treated as success), 3 = error.
     */
    private const STATUS_MAP = [
        1 => 'success',
        2 => 'success',
        3 => 'error',
    ];

    /**
     * Make.com team ID extracted from the provider record.
     * Required for listing and creating scenarios.
     */
    private string $teamId;

    public function __construct(Providers $provider)
    {
        parent::__construct($provider);
        $this->teamId = (string) ($provider->external_account_id ?? '');
    }

    // -------------------------------------------------------------------------
    // Auth
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Make.com uses the "Authorization: Token {token}" scheme.
     */
    protected function defaultHeaders(): array
    {
        return array_merge(parent::defaultHeaders(), [
            'Authorization' => 'Token ' . $this->apiKey,
        ]);
    }

    // -------------------------------------------------------------------------
    // Connection
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v2/users/me as a lightweight connectivity probe.
     */
    public function testConnection(): bool
    {
        try {
            $this->get(self::API_PREFIX . '/users/me');
            return true;
        } catch (\RuntimeException $e) {
            if (in_array($e->getCode(), [401, 403], true)) {
                throw $e;
            }
            return false;
        }
    }

    // -------------------------------------------------------------------------
    // Workflows (= Scenarios) — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Paginates through all Make.com scenarios using offset-based pagination.
     */
    public function listWorkflows(): array
    {
        $results = [];
        $offset  = 0;
        $limit   = 100;

        do {
            $response  = $this->get(self::API_PREFIX . '/scenarios', [
                'teamId'     => $this->teamId,
                'pg[offset]' => $offset,
                'pg[limit]'  => $limit,
            ]);

            $scenarios = $response['scenarios'] ?? [];

            foreach ($scenarios as $scenario) {
                $results[] = $this->normaliseScenario($scenario);
            }

            $pg    = $response['pg'] ?? [];
            $total = (int) ($pg['total'] ?? 0);
            $offset += count($scenarios);
        } while ($offset < $total && count($scenarios) > 0);

        return $results;
    }

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v2/scenarios/{id}
     */
    public function getWorkflow(string $externalId): array
    {
        $response = $this->get(self::API_PREFIX . '/scenarios/' . $externalId);

        return $this->normaliseScenario($response['scenario'] ?? $response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v2/scenarios/{id}/start
     */
    public function activateWorkflow(string $externalId): bool
    {
        $this->post(self::API_PREFIX . '/scenarios/' . $externalId . '/start');

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v2/scenarios/{id}/stop
     */
    public function deactivateWorkflow(string $externalId): bool
    {
        $this->post(self::API_PREFIX . '/scenarios/' . $externalId . '/stop');

        return true;
    }

    // -------------------------------------------------------------------------
    // Scenarios — MakeInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v2/scenarios
     */
    public function createScenario(array $data): array
    {
        $body = ['teamId' => (int) $this->teamId];

        if (isset($data['blueprint'])) {
            $body['blueprint'] = $data['blueprint'];
        }

        if (isset($data['name'])) {
            $body['name'] = $data['name'];
        }

        if (isset($data['scheduling'])) {
            $body['scheduling'] = $data['scheduling'];
        }

        if (isset($data['folder_id'])) {
            $body['folderId'] = (int) $data['folder_id'];
        }

        $response = $this->post(self::API_PREFIX . '/scenarios', $body);

        return $this->normaliseScenario($response['scenario'] ?? $response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls PATCH /api/v2/scenarios/{id}
     */
    public function updateScenario(string $externalId, array $data): array
    {
        $body = [];

        if (isset($data['name'])) {
            $body['name'] = $data['name'];
        }

        if (isset($data['scheduling'])) {
            $body['scheduling'] = $data['scheduling'];
        }

        if (isset($data['blueprint'])) {
            $body['blueprint'] = $data['blueprint'];
        }

        if (isset($data['folder_id'])) {
            $body['folderId'] = (int) $data['folder_id'];
        }

        $response = $this->patch(self::API_PREFIX . '/scenarios/' . $externalId, $body);

        return $this->normaliseScenario($response['scenario'] ?? $response);
    }

    /**
     * {@inheritdoc}
     *
     * Calls DELETE /api/v2/scenarios/{id}
     */
    public function deleteScenario(string $externalId): bool
    {
        $this->delete(self::API_PREFIX . '/scenarios/' . $externalId);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Calls POST /api/v2/scenarios/{id}/run
     */
    public function runScenario(string $externalId, array $inputData = []): array
    {
        $body = [];

        if (!empty($inputData['data'])) {
            $body['data'] = $inputData['data'];
        }

        if (isset($inputData['responsive'])) {
            $body['responsive'] = (bool) $inputData['responsive'];
        }

        if (!empty($inputData['callbackUrl'])) {
            $body['callbackUrl'] = $inputData['callbackUrl'];
        }

        $response = $this->post(self::API_PREFIX . '/scenarios/' . $externalId . '/run', $body);

        return [
            'execution_id' => isset($response['executionId']) ? (string) $response['executionId'] : null,
            'status'       => $response['status'] ?? 'queued',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Calls GET /api/v2/hooks?teamId={teamId}&scenarioId={id}
     */
    public function getScenarioTriggers(string $externalId): array
    {
        $response = $this->get(self::API_PREFIX . '/hooks', [
            'teamId'     => $this->teamId,
            'scenarioId' => $externalId,
        ]);

        $hooks = $response['hooks'] ?? [];

        return array_map(function (array $hook) {
            return [
                'external_id' => (string) ($hook['id'] ?? ''),
                'name'        => (string) ($hook['name'] ?? ''),
                'type'        => (string) ($hook['type'] ?? 'webhook'),
                'url'         => (string) ($hook['url'] ?? ''),
            ];
        }, $hooks);
    }

    // -------------------------------------------------------------------------
    // Executions — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Make.com v2 does not expose a single-execution lookup by ID.
     *
     * @throws \RuntimeException always.
     */
    public function getExecution(string $executionId): array
    {
        throw new \RuntimeException(
            'Make.com API v2 does not support fetching a single execution by ID.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Fetches execution logs for a specific scenario.
     * Requires 'workflow_id' (scenario ID) in $filters.
     *
     * Pagination cursor is the next page offset as a string.
     */
    public function listExecutions(array $filters = []): array
    {
        $scenarioId = $filters['workflow_id'] ?? null;

        if (!$scenarioId) {
            return ['data' => [], 'next_cursor' => null];
        }

        $offset = isset($filters['cursor']) ? (int) $filters['cursor'] : 0;
        $limit  = (int) ($filters['limit'] ?? 100);

        $response = $this->get(
            self::API_PREFIX . '/scenarios/' . $scenarioId . '/logs',
            [
                'pg[offset]' => $offset,
                'pg[limit]'  => $limit,
            ]
        );

        $logs  = $response['scenarioLogs'] ?? [];
        $items = array_map(fn($log) => $this->normaliseLog($log, (string) $scenarioId), $logs);

        $pg         = $response['pg'] ?? [];
        $total      = (int) ($pg['total'] ?? 0);
        $nextOffset = ($offset + count($logs) < $total) ? $offset + count($logs) : null;

        return [
            'data'        => $items,
            'next_cursor' => $nextOffset !== null ? (string) $nextOffset : null,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Make.com v2 does not support deleting individual execution logs.
     *
     * @throws \RuntimeException always.
     */
    public function deleteExecution(string $executionId): bool
    {
        throw new \RuntimeException(
            'Make.com API v2 does not support deleting execution logs.'
        );
    }

    // -------------------------------------------------------------------------
    // Sync — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Fetches all scenarios from Make.com and upserts them into ipaas_workflows.
     */
    public function syncWorkflows(): int
    {
        $scenarios = $this->listWorkflows();
        $count     = 0;

        foreach ($scenarios as $s) {
            Workflows::updateOrCreate(
                [
                    'external_workflow_id' => $s['external_id'],
                    'ipaas_provider_id'    => $this->provider->id,
                ],
                [
                    'name'            => $s['name'],
                    'description'     => null,
                    'trigger_type'    => $s['trigger_type'],
                    'status'          => $s['status'],
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
     * Iterates synced workflows and pulls scenario logs per workflow,
     * stopping pagination when entries older than $since are encountered.
     */
    public function syncExecutions(?\DateTimeInterface $since = null): int
    {
        $since = $since ?? now()->subDay();
        $count = 0;

        $workflows = Workflows::where('ipaas_provider_id', $this->provider->id)->cursor();

        foreach ($workflows as $workflow) {
            $offset = 0;
            $limit  = 100;

            do {
                $response = $this->get(
                    self::API_PREFIX . '/scenarios/' . $workflow->external_workflow_id . '/logs',
                    [
                        'pg[offset]' => $offset,
                        'pg[limit]'  => $limit,
                    ]
                );

                $logs      = $response['scenarioLogs'] ?? [];
                $pg        = $response['pg'] ?? [];
                $total     = (int) ($pg['total'] ?? 0);
                $exhausted = false;

                foreach ($logs as $log) {
                    $started = $log['startedAt'] ?? null;

                    if ($started && strtotime($started) < $since->getTimestamp()) {
                        $exhausted = true;
                        break;
                    }

                    $execution = $this->normaliseLog($log, $workflow->external_workflow_id);

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
                            'error_node'        => null,
                        ]
                    );

                    $count++;
                }

                if ($exhausted) {
                    break;
                }

                $offset += count($logs);
            } while ($offset < $total && count($logs) > 0);
        }

        return $count;
    }

    // -------------------------------------------------------------------------
    // Normalisation helpers
    // -------------------------------------------------------------------------

    /**
     * Map a raw Make.com scenario API response to our internal array shape.
     *
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normaliseScenario(array $raw): array
    {
        $isActive = ($raw['isActive'] ?? false) && !($raw['isPaused'] ?? false);

        $scheduling  = $raw['scheduling'] ?? [];
        $schedType   = $scheduling['type'] ?? 'indefinitely';
        $triggerType = match (true) {
            $schedType === 'indefinitely' && !isset($scheduling['interval']) => 'webhook',
            $schedType === 'ondemand'     => 'manual',
            isset($scheduling['interval']) => 'schedule',
            default                        => 'trigger',
        };

        return [
            'external_id'  => (string) ($raw['id'] ?? ''),
            'name'         => (string) ($raw['name'] ?? ''),
            'description'  => null,
            'status'       => $isActive ? 'active' : 'inactive',
            'trigger_type' => $triggerType,
            'scheduling'   => $scheduling,
            'team_id'      => (string) ($raw['teamId'] ?? $this->teamId),
            'folder_id'    => isset($raw['folderId']) ? (string) $raw['folderId'] : null,
            'created_at'   => $raw['createdAt'] ?? null,
            'updated_at'   => $raw['updatedAt'] ?? null,
        ];
    }

    /**
     * Map a raw Make.com scenario log entry to our internal execution shape.
     *
     * @param  array<string, mixed> $raw
     * @param  string               $scenarioId
     * @return array<string, mixed>
     */
    private function normaliseLog(array $raw, string $scenarioId): array
    {
        $statusCode   = (int) ($raw['status'] ?? 3);
        $status       = self::STATUS_MAP[$statusCode] ?? 'error';
        $errorMessage = ($statusCode === 3 && !empty($raw['errorMessage']))
            ? (string) $raw['errorMessage']
            : null;

        return [
            'external_id'   => (string) ($raw['id'] ?? ''),
            'workflow_id'   => $scenarioId,
            'status'        => $status,
            'trigger_mode'  => 'trigger',
            'started_at'    => $raw['startedAt'] ?? null,
            'finished_at'   => $raw['finishedAt'] ?? null,
            'error_message' => $errorMessage,
            'error_node'    => null,
        ];
    }
}
