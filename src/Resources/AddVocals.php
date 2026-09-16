<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Generates and adds vocals to an uploaded instrumental track.
 */
readonly class AddVocals extends AudioResource
{
    public const ACTION = 'suno/add-vocals';

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'add_vocals', self::ACTION, ['suno-v4.5-plus', 'suno-v5']);
    }
}
