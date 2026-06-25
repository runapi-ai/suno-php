<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\BaseModel;

/**
 * Aligned word response model.
 */
readonly class AlignedWord extends BaseModel
{
    /**
     * Create aligned word timing metadata.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public ?string $word = null,
        public ?bool $success = null,
        public ?float $startTime = null,
        public ?float $endTime = null,
        public ?float $palign = null,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'word' => $word,
            'success' => $success,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'palign' => $palign,
        ] : $raw);
    }

    /**
     * Hydrate aligned word timing metadata from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            word: self::optionalString($raw, 'word'),
            success: self::optionalBool($raw, 'success'),
            startTime: self::optionalFloat($raw, 'start_time'),
            endTime: self::optionalFloat($raw, 'end_time'),
            palign: self::optionalFloat($raw, 'palign'),
            raw: $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    private static function optionalString(array $raw, string $key): ?string
    {
        $value = $raw[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /** @param array<string, mixed> $raw */
    private static function optionalBool(array $raw, string $key): ?bool
    {
        $value = $raw[$key] ?? null;

        return is_bool($value) ? $value : null;
    }

    /** @param array<string, mixed> $raw */
    private static function optionalFloat(array $raw, string $key): ?float
    {
        $value = $raw[$key] ?? null;
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        return null;
    }
}
