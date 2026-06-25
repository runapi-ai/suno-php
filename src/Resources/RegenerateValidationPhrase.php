<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Requests a new, easier validation phrase for an in-progress voice cloning task.
 */
readonly class RegenerateValidationPhrase extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'regenerate_validation_phrase', 'regenerate-validation-phrase');
    }
}
