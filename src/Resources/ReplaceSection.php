<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Re-generates a time range within an existing track with new lyrics and style.
 */
readonly class ReplaceSection extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'replace_section', 'replace-section');
    }
}
