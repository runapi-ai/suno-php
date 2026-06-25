<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\TaskResponse;
use RunApi\Core\Support\Payload;

/**
 * Async audio task response with lifecycle status and output files.
 */
readonly class AudioTaskResponse extends TaskResponse
{
    /**
     * Hydrate an audio task response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(id: Payload::string($raw, 'id'), status: Payload::string($raw, 'status'), error: self::error($raw), raw: $raw);
    }

    /** @param array<string, mixed> $raw */
    protected static function error(array $raw): ?string
    {
        $value = $raw['error'] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new ValidationException('error must be a string');
        }
        return $value;
    }
}
