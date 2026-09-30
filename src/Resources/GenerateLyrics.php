<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\CompletedLyricsTaskResponse;

/**
 * Produces AI-generated lyrics from a text prompt.
 */
readonly class GenerateLyrics extends LyricsResource
{
    /**
     * Submits a lyrics-generation task and returns immediately with a task id.
     *
     * @param array{prompt: string, callback_url?: string} $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Submits a lyrics-generation task and polls until it completes.
     *
     * @param array{prompt: string, callback_url?: string} $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedLyricsTaskResponse
    {
        return parent::run($params, $options);
    }

    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'generate_lyrics');
    }
}
