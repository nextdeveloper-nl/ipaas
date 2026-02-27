<?php

namespace NextDeveloper\IPAAS\Integrations\Make;

use NextDeveloper\IPAAS\Integrations\Contracts\WapInterface;

/**
 * MakeInterface
 *
 * Extends WapInterface with Make.com-specific operations not covered by the
 * generic provider contract.
 *
 * Make.com uses "scenarios" as the equivalent of workflows.
 * Credentials are read from the Providers model:
 *   - $provider->base_url             →  Regional API base (e.g. https://eu1.make.com/api/v2)
 *   - $provider->api_token            →  Make.com API token
 *   - $provider->external_account_id  →  Make.com team ID (required for listing scenarios)
 *
 * API reference: https://developers.make.com/api-documentation
 */
interface MakeInterface extends WapInterface
{
    // -------------------------------------------------------------------------
    // Scenario management (full CRUD + manual run)
    // -------------------------------------------------------------------------

    /**
     * Create a new scenario in the team.
     *
     * Required keys in $data:
     *   - 'blueprint'  (string)  JSON-encoded scenario blueprint
     *
     * Optional keys:
     *   - 'name'        (string)
     *   - 'scheduling'  (array)   e.g. ['type' => 'indefinitely', 'interval' => 900]
     *   - 'folder_id'   (int)
     *
     * @param  array<string, mixed> $data
     * @return array<string, mixed>  normalised scenario
     * @throws \RuntimeException
     */
    public function createScenario(array $data): array;

    /**
     * Update a scenario (name, scheduling, blueprint, folder).
     *
     * @param  string               $externalId  Make.com scenario ID.
     * @param  array<string, mixed> $data
     * @return array<string, mixed>  normalised scenario
     * @throws \RuntimeException
     */
    public function updateScenario(string $externalId, array $data): array;

    /**
     * Permanently delete a scenario from Make.com.
     *
     * @param  string $externalId  Make.com scenario ID.
     * @return bool  true on success.
     * @throws \RuntimeException
     */
    public function deleteScenario(string $externalId): bool;

    /**
     * Manually trigger a scenario execution.
     *
     * Optional keys in $inputData:
     *   - 'data'         (array)  key-value input passed to the first module
     *   - 'responsive'   (bool)   wait for completion before returning (default false)
     *   - 'callbackUrl'  (string) webhook URL to notify on completion
     *
     * @param  string               $externalId  Make.com scenario ID.
     * @param  array<string, mixed> $inputData
     * @return array<string, mixed>  contains 'execution_id' and 'status'
     * @throws \RuntimeException
     */
    public function runScenario(string $externalId, array $inputData = []): array;

    /**
     * Fetch the webhook triggers registered for a scenario.
     *
     * Useful for determining whether a scenario is webhook-triggered and
     * retrieving its webhook URL.
     *
     * Each element contains: 'external_id', 'name', 'type', 'url'.
     *
     * @param  string $externalId  Make.com scenario ID.
     * @return array<int, array<string, mixed>>
     * @throws \RuntimeException
     */
    public function getScenarioTriggers(string $externalId): array;
}
