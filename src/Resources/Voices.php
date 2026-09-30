<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\VoiceCreationResponse;
use RunApi\Suno\Models\VoiceResourceResponse;

/**
 * Creates and reads back the reusable voices a music request can reference by id.
 *
 * A voice is a RunAPI-owned resource: create it from a recording, then keep
 * passing its id wherever a voice is accepted. get() reports whether the voice is
 * ready, which replaces polling a separate availability operation.
 */
readonly class Voices extends SyncResource
{
    private const ENDPOINT = '/api/v1/voices';

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
        parent::__construct($http, self::ENDPOINT, VoiceCreationResponse::class);
    }

    /**
     * Creates a reusable voice from a recording and returns the result.
     *
     * @param array{source_audio_url: string, name?: string} $params
     */
    public function run(array $params, ?RequestOptions $options = null): VoiceCreationResponse
    {
        $response = parent::run($params, $options);

        /** @var VoiceCreationResponse $response */
        return $response;
    }

    /**
     * Fetch a voice resource by its RunAPI-owned id.
     */
    public function get(string $id, ?RequestOptions $options = null): VoiceResourceResponse
    {
        return VoiceResourceResponse::fromArray($this->http->request('get', self::ENDPOINT . '/' . rawurlencode($id), [
            'options' => $options,
        ]));
    }
}
