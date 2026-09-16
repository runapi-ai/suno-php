<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\CompletedMusicVisualizationResponse;
use RunApi\Suno\Models\MusicVisualizationResponse;

/**
 * Renders a visualization video for an existing track.
 */
readonly class MusicVisualizations extends AudioResource
{
    public const ACTION = 'suno/music-visualizations';

    private const ENDPOINT = '/api/v1/music_visualizations';

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
            MusicVisualizationResponse::class,
            CompletedMusicVisualizationResponse::class,
        );
    }

    /**
     * Fetch the current status of a visualization request by id.
     */
    public function get(string $id, ?RequestOptions $options = null): MusicVisualizationResponse
    {
        $response = parent::get($id, $options);

        /** @var MusicVisualizationResponse $response */
        return $response;
    }

    /**
     * Submit a visualization request and poll until it completes.
     *
     * @param array<string, mixed> $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedMusicVisualizationResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedMusicVisualizationResponse $response */
        return $response;
    }
}
