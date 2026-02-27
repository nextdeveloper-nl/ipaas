<?php

namespace NextDeveloper\IPAAS\Integrations;

use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Integrations\Contracts\WapInterface;
use NextDeveloper\IPAAS\Integrations\Make\MakeIntegration;
use NextDeveloper\IPAAS\Integrations\N8N\N8NIntegration;
use NextDeveloper\IPAAS\Integrations\Zapier\ZapierIntegration;

/**
 * WapIntegrationFactory
 *
 * Resolves the correct WapInterface implementation from a Providers model
 * based on its provider_type field.
 *
 * Usage:
 *   $integration = WapIntegrationFactory::make($provider);
 *   $integration->syncWorkflows();
 *
 * To add support for a new provider, register its provider_type string and
 * the corresponding class in the $registry array below.
 */
class WapIntegrationFactory
{
    /**
     * Map of provider_type values to their integration class names.
     * Add new providers here as they are implemented.
     *
     * @var array<string, class-string<WapInterface>>
     */
    private static array $registry = [
        'n8n'    => N8NIntegration::class,
        'make'   => MakeIntegration::class,
        'zapier' => ZapierIntegration::class,
    ];

    /**
     * Create and return the appropriate WapInterface for the given provider.
     *
     * @param  Providers $provider
     * @return WapInterface
     * @throws \InvalidArgumentException  if the provider_type is not registered.
     */
    public static function make(Providers $provider): WapInterface
    {
        $type = strtolower((string) $provider->provider_type);

        if (!array_key_exists($type, self::$registry)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'No integration registered for provider_type "%s". ' .
                    'Registered types: %s',
                    $type,
                    implode(', ', array_keys(self::$registry))
                )
            );
        }

        $class = self::$registry[$type];

        return new $class($provider);
    }

    /**
     * Register a custom integration class for a provider type at runtime.
     * Useful for testing or third-party extensions.
     *
     * @param  string                      $providerType
     * @param  class-string<WapInterface>  $class
     */
    public static function register(string $providerType, string $class): void
    {
        self::$registry[strtolower($providerType)] = $class;
    }

    /**
     * Return all registered provider types.
     *
     * @return string[]
     */
    public static function registeredTypes(): array
    {
        return array_keys(self::$registry);
    }
}
