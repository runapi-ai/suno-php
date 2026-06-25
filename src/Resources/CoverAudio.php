<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Re-records vocals over an uploaded audio file with a new style or voice.
 */
readonly class CoverAudio extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'cover_audio', 'cover-audio', ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5']);
    }
}
