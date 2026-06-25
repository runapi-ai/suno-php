<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\BoostStyleResponse;

/**
 * Generates style/genre tags from a text description for use in style fields. Synchronous (run() only).
 */
readonly class BoostStyle extends SyncResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http);
    }

    /**
     * Create a resource using the shared RunAPI HTTP transport.
     */
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, '/api/v1/suno/boost_style', 'suno/boost-style', BoostStyleResponse::class, ['description']);
    }

    /**
     * Generates style/genre tags from a text description and returns the result.
     *
     * @param array{description: string, name?: string} $params
     */
    public function run(array $params, ?RequestOptions $options = null): BoostStyleResponse
    {
        $response = parent::run($params, $options);

        /** @var BoostStyleResponse $response */
        return $response;
    }
}
