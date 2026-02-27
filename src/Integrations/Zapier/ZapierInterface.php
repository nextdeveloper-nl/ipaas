<?php

namespace NextDeveloper\IPAAS\Integrations\Zapier;

use NextDeveloper\IPAAS\Integrations\Contracts\WapInterface;

/**
 * ZapierInterface
 *
 * Extends WapInterface for Zapier-specific operations.
 *
 * Note: The Zapier REST API v2 is primarily read-only. Zap creation and
 * deletion are not supported via the public API. Execution history is not
 * publicly available (experimental endpoints only).
 *
 * Credentials are read from the Providers model:
 *   - $provider->base_url  →  Zapier API base (defaults to https://api.zapier.com/v2)
 *   - $provider->api_token →  Zapier API token (OAuth personal access token)
 *
 * API reference: https://zapier.com/developer/documentation/v2/rest-hooks/
 */
interface ZapierInterface extends WapInterface
{
    // No Zapier-specific methods beyond WapInterface at this time.
    // Placeholder for future API extensions (e.g. REST Hooks management).
}
