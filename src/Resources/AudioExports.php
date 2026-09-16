<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\AudioExportResponse;
use RunApi\Suno\Models\CompletedAudioExportResponse;

/**
 * Exports an existing track to a downloadable audio file.
 */
readonly class AudioExports extends AudioResource
{
    public const ACTION = 'suno/audio-exports';

    private const ENDPOINT = '/api/v1/audio_exports';

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            self::ENDPOINT,
            self::ACTION,
            [],
            AudioExportResponse::class,
            CompletedAudioExportResponse::class,
        );
    }

    /**
     * Fetch the current status of an audio export by id.
     */
    public function get(string $id, ?RequestOptions $options = null): AudioExportResponse
    {
        $response = parent::get($id, $options);

        /** @var AudioExportResponse $response */
        return $response;
    }

    /**
     * Submit an audio export and poll until it completes.
     *
     * @param array<string, mixed> $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedAudioExportResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedAudioExportResponse $response */
        return $response;
    }
}
