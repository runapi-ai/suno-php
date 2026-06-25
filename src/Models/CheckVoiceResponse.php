<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;

/**
 * Async voice task response with lifecycle status and output files.
 */
readonly class CheckVoiceResponse extends BaseModel
{
    /**
     * Create a voice readiness response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(public ?bool $isAvailable = null, public ?string $error = null, array $raw = [])
    {
        parent::__construct($raw === [] ? ['is_available' => $isAvailable, 'error' => $error] : $raw);
    }

    /**
     * Hydrate a voice readiness response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(isAvailable: self::optionalBool($raw, 'is_available'), error: self::error($raw), raw: $raw);
    }

    /** @param array<string, mixed> $raw */
    private static function optionalBool(array $raw, string $key): ?bool
    {
        $value = $raw[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_bool($value)) {
            throw new ValidationException($key . ' must be a boolean');
        }

        return $value;
    }

    /** @param array<string, mixed> $raw */
    private static function error(array $raw): ?string
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
