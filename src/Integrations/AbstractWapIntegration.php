<?php

namespace NextDeveloper\IPAAS\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;
use NextDeveloper\IPAAS\Database\Models\Providers;
use NextDeveloper\IPAAS\Integrations\Contracts\WapInterface;

/**
 * AbstractWapIntegration
 *
 * Base class for all Workflow Automation Provider integrations. Provides a
 * pre-configured HTTP client (built from the Providers model) and a set of
 * convenience wrappers (get, post, patch, delete) with unified error handling
 * and logging.
 *
 * Concrete classes must implement WapInterface and call parent::__construct()
 * to initialise the provider context.
 *
 * Credentials are read from the Providers model fields:
 *   - $provider->region              →  API base URL
 *   - $provider->external_account_id →  API key / token
 */
abstract class AbstractWapIntegration implements WapInterface
{
    /**
     * The provider DB record that this integration instance is bound to.
     */
    protected Providers $provider;

    /**
     * Resolved base URL (trimmed, no trailing slash).
     */
    protected string $baseUrl;

    /**
     * API key extracted from the provider record.
     */
    protected string $apiKey;

    /**
     * Default request timeout in seconds.
     */
    protected int $timeout = 30;

    public function __construct(Providers $provider)
    {
        $this->provider = $provider;
        $this->baseUrl  = rtrim((string) ($provider->base_url ?: $provider->region), '/');
        $this->apiKey   = (string) $provider->api_token;
    }

    // -------------------------------------------------------------------------
    // HTTP helpers
    // -------------------------------------------------------------------------

    /**
     * Build a PendingRequest with auth headers and base URL already set.
     * Concrete classes can override this to add provider-specific headers.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withHeaders($this->defaultHeaders())
            ->acceptJson();
    }

    /**
     * Return the authentication headers used for every request.
     * Override in concrete classes to change the auth scheme.
     *
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * Perform a GET request and return the decoded response body.
     *
     * @param  string               $path
     * @param  array<string, mixed> $query
     * @return array<string, mixed>
     * @throws \RuntimeException
     */
    protected function get(string $path, array $query = []): array
    {
        $response = $this->client()->get($path, $query);

        return $this->decode($response, 'GET', $path);
    }

    /**
     * Perform a POST request and return the decoded response body.
     *
     * @param  string               $path
     * @param  array<string, mixed> $body
     * @return array<string, mixed>
     * @throws \RuntimeException
     */
    protected function post(string $path, array $body = []): array
    {
        $response = $this->client()->post($path, $body);

        return $this->decode($response, 'POST', $path);
    }

    /**
     * Perform a PATCH request and return the decoded response body.
     *
     * @param  string               $path
     * @param  array<string, mixed> $body
     * @return array<string, mixed>
     * @throws \RuntimeException
     */
    protected function patch(string $path, array $body = []): array
    {
        $response = $this->client()->patch($path, $body);

        return $this->decode($response, 'PATCH', $path);
    }

    /**
     * Perform a PUT request and return the decoded response body.
     *
     * @param  string               $path
     * @param  array<string, mixed> $body
     * @return array<string, mixed>
     * @throws \RuntimeException
     */
    protected function put(string $path, array $body = []): array
    {
        $response = $this->client()->put($path, $body);

        return $this->decode($response, 'PUT', $path);
    }

    /**
     * Perform a DELETE request and return the decoded response body.
     *
     * @param  string $path
     * @return array<string, mixed>
     * @throws \RuntimeException
     */
    protected function delete(string $path): array
    {
        $response = $this->client()->delete($path);

        return $this->decode($response, 'DELETE', $path);
    }

    // -------------------------------------------------------------------------
    // Response handling
    // -------------------------------------------------------------------------

    /**
     * Assert the response is successful and return its JSON body as an array.
     *
     * @throws \RuntimeException  on 4xx/5xx responses.
     */
    protected function decode(Response $response, string $method, string $path): array
    {
        if ($response->failed()) {
            $status = $response->status();
            $body   = $response->body();

            throw new \RuntimeException(
                sprintf(
                    '[%s] %s %s failed with HTTP %d: %s',
                    class_basename($this),
                    $method,
                    $path,
                    $status,
                    $body
                ),
                $status
            );
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Return the Providers model this integration is bound to.
     */
    public function getProvider(): Providers
    {
        return $this->provider;
    }
}
