<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Support\Payload;

/**
 * Completed music from sample task returned by run(); the generated tracks are guaranteed present.
 */
readonly class CompletedMusicFromSampleResponse extends CompletedAudioTaskResponse
{
    /**
     * Create a completed music from sample response value object.
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
     * Hydrate a completed music from sample response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            status: Payload::string($raw, 'status'),
            error: self::error($raw),
            audios: Payload::listOf($raw, 'audios', static fn (array $audio): array => $audio, true),
            raw: $raw,
        );
    }

    /**
     * Narrow a polled task response after completion has been confirmed.
     */
    public static function fromResponse(AudioTaskResponse $response): self
    {
        return self::fromArray($response->toArray());
    }
}
