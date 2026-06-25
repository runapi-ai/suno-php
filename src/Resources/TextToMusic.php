<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\CompletedAudioTaskResponse;

/**
 * Generates songs from a text prompt with configurable vocal mode, style, and persona.
 */
readonly class TextToMusic extends AudioResource
{
    /**
     * Submits a song-generation task and returns immediately with a task id.
     *
     * @param array{
     *   model: string,
     *   vocal_mode: string,
     *   prompt?: string,
     *   lyrics?: string,
     *   style?: string,
     *   title?: string,
     *   callback_url?: string,
     *   duration_seconds?: int,
     *   continue_at?: float|int,
     *   persona_id?: string,
     *   persona_type?: string,
     *   vocal_gender?: string,
     *   negative_tags?: string,
     *   rules?: string,
     *   audio_weight?: float|int,
     *   style_weight?: float|int,
     *   weirdness_constraint?: float|int
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Submits a song-generation task and polls until it completes.
     *
     * @param array{
     *   model: string,
     *   vocal_mode: string,
     *   prompt?: string,
     *   lyrics?: string,
     *   style?: string,
     *   title?: string,
     *   callback_url?: string,
     *   duration_seconds?: int,
     *   continue_at?: float|int,
     *   persona_id?: string,
     *   persona_type?: string,
     *   vocal_gender?: string,
     *   negative_tags?: string,
     *   rules?: string,
     *   audio_weight?: float|int,
     *   style_weight?: float|int,
     *   weirdness_constraint?: float|int
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedAudioTaskResponse
    {
        return parent::run($params, $options);
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'text_to_music', 'text-to-music', ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5']);
    }
}
