<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Creates cover artwork for an existing music task.
 */
readonly class GenerateArtwork extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'generate_artwork', 'generate-artwork');
    }
}
