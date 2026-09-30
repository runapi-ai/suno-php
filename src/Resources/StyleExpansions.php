<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\BoostStyleResponse;

/**
 * Expands a style description into genre tags for use in style fields. Synchronous (run() only).
 */
readonly class StyleExpansions extends SyncResource
{
    private const ENDPOINT = '/api/v1/style_expansions';

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
        parent::__construct($http, self::ENDPOINT, BoostStyleResponse::class);
    }

    /**
     * Expands a style description into genre tags and returns the result.
     *
     * @param array{description: string} $params
     */
    public function run(array $params, ?RequestOptions $options = null): BoostStyleResponse
    {
        $response = parent::run($params, $options);

        /** @var BoostStyleResponse $response */
        return $response;
    }
}
