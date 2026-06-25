<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\GeneratePersonaResponse;

/**
 * Creates a reusable style or voice persona from an existing track's vocals. Synchronous (run() only).
 */
readonly class GeneratePersona extends SyncResource
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
        parent::__construct(
            $http,
            '/api/v1/suno/generate_persona',
            'suno/generate-persona',
            GeneratePersonaResponse::class,
            ['task_id', 'audio_id', 'name', 'description'],
        );
    }

    /**
     * Creates a reusable style or voice persona from an existing track and returns the result.
     *
     * @param array{task_id: string, audio_id: string, name: string, description: string} $params
     */
    public function run(array $params, ?RequestOptions $options = null): GeneratePersonaResponse
    {
        $response = parent::run($params, $options);

        /** @var GeneratePersonaResponse $response */
        return $response;
    }
}
