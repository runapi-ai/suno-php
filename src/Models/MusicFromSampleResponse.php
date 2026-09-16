<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Support\Payload;

/**
 * Music from sample task response: the polled task status plus the generated tracks once it completes.
 */
readonly class MusicFromSampleResponse extends AudioTaskResponse
{
    /**
     * Create a music from sample response value object.
     *
     * @param list<array<string, mixed>> $audios
     * @param array<string, mixed> $raw
     */
    public function __construct(
        ?string $id,
        string $status,
        ?string $error = null,
        public array $audios = [],
        array $raw = [],
    ) {
        parent::__construct(id: $id, status: $status, error: $error, raw: $raw);
    }

    /**
     * Hydrate a music from sample response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            status: Payload::string($raw, 'status'),
            error: self::error($raw),
            audios: self::audios($raw),
            raw: $raw,
        );
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return list<array<string, mixed>>
     */
    protected static function audios(array $raw, bool $required = false): array
    {
        return Payload::listOf($raw, 'audios', static fn (array $audio): array => $audio, $required);
    }
}
