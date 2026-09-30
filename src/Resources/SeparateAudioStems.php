<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\CompletedSeparateAudioStemsResponse;
use RunApi\Suno\Models\SeparateAudioStemsResponse;

/**
 * Splits a track into individual instrument stems (vocals, drums, bass, guitar, etc.).
 */
readonly class SeparateAudioStems extends AudioResource
{
    /** Fetch the current status of a stem-separation task. */
    public function get(string $id, ?RequestOptions $options = null): SeparateAudioStemsResponse
    {
        $response = parent::get($id, $options);

        /** @var SeparateAudioStemsResponse $response */
        return $response;
    }

    /** Submit a stem-separation task and poll until it completes. */
    public function run(array $params, ?RequestOptions $options = null): CompletedSeparateAudioStemsResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedSeparateAudioStemsResponse $response */
        return $response;
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            'separate_audio_stems',
            responseClass: SeparateAudioStemsResponse::class,
            completedResponseClass: CompletedSeparateAudioStemsResponse::class,
        );
    }
}
