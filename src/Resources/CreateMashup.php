<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Blends two audio tracks into a single new composition.
 */
readonly class CreateMashup extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'create_mashup', 'create-mashup', ['suno-v4', 'suno-v4.5', 'suno-v4.5-all', 'suno-v4.5-plus', 'suno-v5', 'suno-v5.5']);
    }
}
