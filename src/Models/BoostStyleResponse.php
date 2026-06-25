<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;

/**
 * Async boost style response task response with lifecycle status and output files.
 */
readonly class BoostStyleResponse extends BaseModel
{
    /**
     * Create a style boost response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(public ?string $style = null, public ?string $error = null, array $raw = [])
    {
        parent::__construct($raw === [] ? ['style' => $style, 'error' => $error] : $raw);
    }

    /**
     * Hydrate a style boost response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(style: self::optionalString($raw, 'style'), error: self::error($raw), raw: $raw);
    }

    /** @param array<string, mixed> $raw */
    private static function optionalString(array $raw, string $key): ?string
    {
        $value = $raw[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new ValidationException($key . ' must be a string');
        }

        return $value;
    }

    /** @param array<string, mixed> $raw */
    private static function error(array $raw): ?string
    {
        return self::optionalString($raw, 'error');
    }
}
