<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Support\Payload;

/** Completed lyrics task response returned by run(). */
readonly class CompletedLyricsTaskResponse extends LyricsTaskResponse
{
    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            status: Payload::string($raw, 'status'),
            error: self::error($raw),
            lyrics: self::lyrics($raw, required: true),
            raw: $raw,
        );
    }

    public static function fromResponse(LyricsTaskResponse $response): self
    {
        return self::fromArray($response->toArray());
    }
}
