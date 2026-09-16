<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\CompletedMusicFromSampleResponse;
use RunApi\Suno\Models\MusicFromSampleResponse;

/**
 * Creates music that samples a range of an uploaded audio file.
 */
readonly class MusicFromSample extends AudioResource
{
    public const ACTION = 'suno/music-from-sample';

    private const ENDPOINT = '/api/v1/music_from_sample';

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

    protected function validate(array $params, string $model): void
    {
        parent::validate($params, $model);
        $start = $params['start_seconds'] ?? null;
        $end = $params['end_seconds'] ?? null;
        if ((is_int($start) || is_float($start)) && (is_int($end) || is_float($end)) && $end <= $start) {
            throw new ValidationException('end_seconds must be greater than start_seconds');
        }
    }
}
