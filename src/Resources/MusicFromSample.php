<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\CompletedMusicFromSampleResponse;
use RunApi\Suno\Models\MusicFromSampleResponse;

/**
 * Creates music that samples a range of an uploaded audio file.
 */
readonly class MusicFromSample extends AudioResource
{
    private const ENDPOINT = '/api/v1/music_from_sample';

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            self::ENDPOINT,
            MusicFromSampleResponse::class,
            CompletedMusicFromSampleResponse::class,
        );
    }

    /**
     * Fetch the current status of a music from sample request by id.
     */
    public function get(string $id, ?RequestOptions $options = null): MusicFromSampleResponse
    {
        $response = parent::get($id, $options);

        /** @var MusicFromSampleResponse $response */
        return $response;
    }

    /**
     * Submit a music from sample request and poll until it completes.
     *
     * @param array<string, mixed> $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedMusicFromSampleResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedMusicFromSampleResponse $response */
        return $response;
    }
}
