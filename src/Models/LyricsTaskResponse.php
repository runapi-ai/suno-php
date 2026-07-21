<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\TaskResponse;
use RunApi\Core\Support\Payload;

/** Async lyrics task response with generated lyrics entries. */
readonly class LyricsTaskResponse extends TaskResponse
{
    /**
     * @param list<Lyric> $lyrics
     * @param array<string, mixed> $raw
     */
    public function __construct(?string $id, string $status, ?string $error = null, public array $lyrics = [], array $raw = [])
    {
        parent::__construct(
            id: $id,
            status: $status,
            error: $error,
            raw: $raw === [] ? [
                'id' => $id,
                'status' => $status,
                'error' => $error,
                'lyrics' => array_map(static fn (Lyric $lyric): array => $lyric->toArray(), $lyrics),
            ] : $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            status: Payload::string($raw, 'status'),
            error: self::error($raw),
            lyrics: self::lyrics($raw),
            raw: $raw,
        );
    }

    /**
     * @param array<string, mixed> $raw
     * @return list<Lyric>
     */
    protected static function lyrics(array $raw, bool $required = false): array
    {
        return Payload::listOf($raw, 'lyrics', Lyric::fromArray(...), $required);
    }
}
