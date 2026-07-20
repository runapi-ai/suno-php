<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/** One audio result in an advanced stem extraction pair. */
readonly class AdvancedStemAudio extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        public string $id,
        public float $durationSeconds,
        public string $audioUrl,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'id' => $id,
            'duration_seconds' => $durationSeconds,
            'audio_url' => $audioUrl,
        ] : $raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            durationSeconds: self::number($raw, 'duration_seconds'),
            audioUrl: Payload::string($raw, 'audio_url'),
            raw: $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    private static function number(array $raw, string $key): float
    {
        $value = $raw[$key] ?? null;
        if (!is_int($value) && !is_float($value)) {
            throw new ValidationException($key . ' must be numeric');
        }

        return (float) $value;
    }
}
