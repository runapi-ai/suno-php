<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Starts the voice cloning pipeline by extracting a validation phrase from a voice recording.
 */
readonly class VoiceToValidationPhrase extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'voice_to_validation_phrase', 'voice-to-validation-phrase');
    }
}
