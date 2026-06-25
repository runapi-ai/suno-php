<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\GetTimestampedLyricsResponse;

/**
 * Retrieves word-level timing alignment for a track. Synchronous (run() only).
 */
readonly class GetTimestampedLyrics extends SyncResource
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
            '/api/v1/suno/get_timestamped_lyrics',
            'suno/get-timestamped-lyrics',
            GetTimestampedLyricsResponse::class,
            ['task_id', 'audio_id'],
        );
    }

    /**
     * Retrieves word-level timing alignment for a track and returns the result.
     *
     * @param array{task_id: string, audio_id: string} $params
     */
    public function run(array $params, ?RequestOptions $options = null): GetTimestampedLyricsResponse
    {
        $response = parent::run($params, $options);

        /** @var GetTimestampedLyricsResponse $response */
        return $response;
    }
}
