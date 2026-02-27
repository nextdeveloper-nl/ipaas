<?php

namespace NextDeveloper\IPAAS\Integrations\Zapier;

use NextDeveloper\IPAAS\Database\Models\Workflows;
use NextDeveloper\IPAAS\Integrations\AbstractWapIntegration;

/**
 * ZapierIntegration
 *
 * Implements ZapierInterface for Zapier using the Zapier REST API v2.
 *
 * API reference: https://zapier.com/developer/documentation/v2/rest-hooks/
 *
 * Limitations:
 *   - Zap creation and deletion are not supported via the public API.
 *   - Execution history is not publicly available; syncExecutions() returns 0.
 *
 * Credentials are read from the Providers model:
 *   - $provider->base_url  →  Zapier API base (defaults to https://api.zapier.com/v2)
 *   - $provider->api_token →  Zapier personal access token
 *
 * Authentication uses the "Authorization: Bearer {token}" header.
 */
class ZapierIntegration extends AbstractWapIntegration implements ZapierInterface
{
    /**
     * Default Zapier v2 API base URL used when provider->base_url is empty.
     */
    private const DEFAULT_BASE_URL = 'https://api.zapier.com/v2';

    public function __construct(\NextDeveloper\IPAAS\Database\Models\Providers $provider)
    {
        parent::__construct($provider);

        // Fall back to the canonical Zapier v2 base if none is stored
        if (empty($this->baseUrl)) {
            $this->baseUrl = self::DEFAULT_BASE_URL;
        }
    }

    // -------------------------------------------------------------------------
    // Auth
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Zapier uses the "Authorization: Bearer {token}" scheme.
     */
    protected function defaultHeaders(): array
    {
        return array_merge(parent::defaultHeaders(), [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);
    }

    // -------------------------------------------------------------------------
    // Connection
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Calls GET /zaps?limit=1 as a lightweight connectivity probe.
     */
    public function testConnection(): bool
    {
        try {
            $this->get('/zaps', ['limit' => 1]);
            return true;
        } catch (\RuntimeException $e) {
            if (in_array($e->getCode(), [401, 403], true)) {
                throw $e;
            }
            return false;
        }
    }

    // -------------------------------------------------------------------------
    // Workflows (= Zaps) — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Paginates through all Zapier zaps using offset-based pagination.
     */
    public function listWorkflows(): array
    {
        $results = [];
        $offset  = 0;
        $limit   = 100;

        do {
            $response = $this->get('/zaps', [
                'limit'  => $limit,
                'offset' => $offset,
            ]);

            $zaps = $response['results'] ?? $response['zaps'] ?? [];

            foreach ($zaps as $zap) {
                $results[] = $this->normaliseZap($zap);
            }

            $offset += count($zaps);
            $total   = (int) ($response['count'] ?? 0);
        } while ($offset < $total && count($zaps) > 0);

        return $results;
    }

    /**
     * {@inheritdoc}
     *
     * Calls GET /zaps/{id}
     */
    public function getWorkflow(string $externalId): array
    {
        $response = $this->get('/zaps/' . $externalId);

        return $this->normaliseZap($response);
    }

    /**
     * {@inheritdoc}
     *
     * Enables a zap via PATCH /zaps/{id} with {"is_enabled": true}
     */
    public function activateWorkflow(string $externalId): bool
    {
        $this->patch('/zaps/' . $externalId, ['is_enabled' => true]);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Disables a zap via PATCH /zaps/{id} with {"is_enabled": false}
     */
    public function deactivateWorkflow(string $externalId): bool
    {
        $this->patch('/zaps/' . $externalId, ['is_enabled' => false]);

        return true;
    }

    // -------------------------------------------------------------------------
    // Executions — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Zapier does not expose execution history via the public API.
     *
     * @throws \RuntimeException always.
     */
    public function getExecution(string $executionId): array
    {
        throw new \RuntimeException(
            'Zapier API v2 does not expose execution history via the public API.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Zapier does not expose execution history via the public API.
     * Returns an empty result set instead of throwing so callers can handle
     * it gracefully.
     */
    public function listExecutions(array $filters = []): array
    {
        return ['data' => [], 'next_cursor' => null];
    }

    /**
     * {@inheritdoc}
     *
     * Zapier does not support deleting executions via the public API.
     *
     * @throws \RuntimeException always.
     */
    public function deleteExecution(string $executionId): bool
    {
        throw new \RuntimeException(
            'Zapier API v2 does not support deleting executions.'
        );
    }

    // -------------------------------------------------------------------------
    // Sync — WapInterface
    // -------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     *
     * Fetches all zaps from Zapier and upserts them into ipaas_workflows.
     */
    public function syncWorkflows(): int
    {
        $zaps  = $this->listWorkflows();
        $count = 0;

        foreach ($zaps as $zap) {
            Workflows::updateOrCreate(
                [
                    'external_workflow_id' => $zap['external_id'],
                    'ipaas_provider_id'    => $this->provider->id,
                ],
                [
                    'name'            => $zap['name'],
                    'description'     => null,
                    'trigger_type'    => $zap['trigger_type'],
                    'status'          => $zap['status'],
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
     * Zapier does not expose execution history via the public API.
     * Logs a notice and returns 0.
     */
    public function syncExecutions(?\DateTimeInterface $since = null): int
    {
        \Illuminate\Support\Facades\Log::notice(
            '[IPAAS] Zapier execution sync skipped: execution history is not available via the public Zapier API v2.',
            ['provider_id' => $this->provider->uuid ?? $this->provider->id]
        );

        return 0;
    }

    // -------------------------------------------------------------------------
    // Normalisation helpers
    // -------------------------------------------------------------------------

    /**
     * Map a raw Zapier zap API response to our internal array shape.
     *
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function normaliseZap(array $raw): array
    {
        $isEnabled = (bool) ($raw['is_enabled'] ?? ($raw['state'] ?? '') === 'on');

        return [
            'external_id'  => (string) ($raw['id'] ?? ''),
            'name'         => (string) ($raw['title'] ?? $raw['name'] ?? ''),
            'description'  => null,
            'status'       => $isEnabled ? 'active' : 'inactive',
            'trigger_type' => 'trigger',
            'created_at'   => $raw['created_at'] ?? null,
            'updated_at'   => $raw['modified_at'] ?? $raw['updated_at'] ?? null,
        ];
    }
}
