<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\CompletedLyricsTaskResponse;

/** Blends two caller-authored lyrics texts. */
readonly class BlendLyrics extends LyricsResource
{
    /** @param array{lyrics_a: string, lyrics_b: string, callback_url?: string} $params */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /** @param array{lyrics_a: string, lyrics_b: string, callback_url?: string} $params */
    public function run(array $params, ?RequestOptions $options = null): CompletedLyricsTaskResponse
    {
        return parent::run($params, $options);
    }

    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'blend_lyrics');
    }
}
