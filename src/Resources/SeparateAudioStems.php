<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Splits a track into individual instrument stems (vocals, drums, bass, guitar, etc.).
 */
readonly class SeparateAudioStems extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'separate_audio_stems', 'separate-audio-stems');
    }
}
