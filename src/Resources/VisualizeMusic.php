<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Generates a music visualization video from an existing track.
 *
 * @deprecated Use MusicVisualizations instead.
 */
readonly class VisualizeMusic extends AudioResource
{
    public const ACTION = 'suno/visualize-music';

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'visualize_music', self::ACTION);
    }
}
