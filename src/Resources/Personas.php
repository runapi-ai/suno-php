<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\HybridResource;
use RunApi\Suno\Models\PersonaCreationResponse;
use RunApi\Suno\Models\PersonaResourceResponse;

/**
 * Creates and reads back the reusable personas a music request can reference by id.
 *
 * A persona is a RunAPI-owned resource: create it once, then keep passing its id
 * in the persona_id field of music generation parameters. Holding the resource id
 * instead of the request that produced it keeps a workflow resumable after that
 * request is gone.
 */
readonly class Personas extends HybridResource
{
    public const ACTION = 'suno/personas';

    private const ENDPOINT = '/api/v1/personas';

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, self::ENDPOINT, self::ACTION, PersonaCreationResponse::class);
    }

    /**
     * Create a persona, following a request the service accepted for local execution to its stored result.
     *
     * @param array{source_task_id: string, source_audio_id: string, name: string, description: string} $params
     */
    public function run(array $params, ?RequestOptions $options = null): PersonaCreationResponse
    {
        $response = parent::run($params, $options);

        /** @var PersonaCreationResponse $response */
        return $response;
    }

    /**
     * Fetch a persona resource by its RunAPI-owned id.
     */
    public function get(string $id, ?RequestOptions $options = null): PersonaResourceResponse
    {
        return PersonaResourceResponse::fromArray($this->http->request('get', self::ENDPOINT . '/' . rawurlencode($id), [
            'options' => $options,
        ]));
    }
}
