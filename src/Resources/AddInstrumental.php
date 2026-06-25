<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Generates and adds an instrumental backing track to uploaded audio.
 */
readonly class AddInstrumental extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'add_instrumental', 'add-instrumental', ['suno-v4.5-plus', 'suno-v5', 'suno-v5.5']);
    }
}
