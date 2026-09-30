<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Converts a generated track to WAV format.
 *
 * @deprecated Use AudioExports instead, which export a RunAPI audio resource.
 */
readonly class ConvertAudio extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'convert_audio');
    }
}
