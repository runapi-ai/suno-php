<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Generates sound effects (not music) from a text description with optional looping and BPM control.
 */
readonly class TextToSound extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'text_to_sound', 'text-to-sound', ['suno-v5', 'suno-v5.5']);
    }
}
