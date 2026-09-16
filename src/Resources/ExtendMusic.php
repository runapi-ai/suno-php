<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Continues an existing track from a specified timestamp, inheriting or overriding its settings.
 */
readonly class ExtendMusic extends AudioResource
{
    public const ACTION = 'suno/extend-music';

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'extend_music', self::ACTION, ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5']);
    }
}
