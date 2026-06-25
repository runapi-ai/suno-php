<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;

/**
 * Async lyrics task response with lifecycle status and output files.
 */
readonly class GetTimestampedLyricsResponse extends BaseModel
{
    /**
     * Create a timestamped lyrics response value object.
     *
     * @param list<AlignedWord> $alignedWords
     * @param list<float> $waveformData
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public array $alignedWords = [],
        public array $waveformData = [],
        public ?float $hootCer = null,
        public ?bool $isStreamed = null,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'aligned_words' => array_map(static fn (AlignedWord $word): array => $word->toArray(), $alignedWords),
            'waveform_data' => $waveformData,
            'hoot_cer' => $hootCer,
            'is_streamed' => $isStreamed,
        ] : $raw);
    }

    /**
     * Hydrate a timestamped lyrics response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            alignedWords: self::alignedWords($raw),
            waveformData: self::floatList($raw, 'waveform_data'),
            hootCer: self::optionalFloat($raw, 'hoot_cer'),
            isStreamed: self::optionalBool($raw, 'is_streamed'),
            raw: $raw,
        );
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return list<AlignedWord>
     */
    private static function alignedWords(array $raw): array
    {
        $value = $raw['aligned_words'] ?? [];
        if (!is_array($value)) {
            throw new ValidationException('aligned_words must be an array');
        }

        return array_map(static function (mixed $item): AlignedWord {
            if (!is_array($item)) {
                throw new ValidationException('aligned_words must contain objects');
            }

            /** @var array<string, mixed> $item */
            return AlignedWord::fromArray($item);
        }, array_values($value));
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return list<float>
     */
    private static function floatList(array $raw, string $key): array
    {
        $value = $raw[$key] ?? [];
        if (!is_array($value)) {
            throw new ValidationException($key . ' must be an array');
        }

        return array_map(static function (mixed $item) use ($key): float {
            if (!is_int($item) && !is_float($item)) {
                throw new ValidationException($key . ' must contain numbers');
            }

            return (float) $item;
        }, array_values($value));
    }

    /** @param array<string, mixed> $raw */
    private static function optionalFloat(array $raw, string $key): ?float
    {
        $value = $raw[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_int($value) && !is_float($value)) {
            throw new ValidationException($key . ' must be numeric');
        }

        return (float) $value;
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
}
