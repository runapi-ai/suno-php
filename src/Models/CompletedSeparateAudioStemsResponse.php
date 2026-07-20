<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Support\Payload;

/** Completed stem-separation response with separated audio guaranteed present. */
readonly class CompletedSeparateAudioStemsResponse extends CompletedAudioTaskResponse
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        ?string $id,
        string $status,
        ?string $error,
        public SeparatedAudio $separatedAudios,
        array $raw = [],
    ) {
        parent::__construct(id: $id, status: $status, error: $error, raw: $raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::optionalString($raw, 'id'),
            status: Payload::string($raw, 'status'),
            error: self::error($raw),
            separatedAudios: SeparatedAudio::fromArray(Payload::array($raw, 'separated_audios')),
            raw: $raw,
        );
    }

    /** Narrow a polled response after completion has been confirmed. */
    public static function fromResponse(AudioTaskResponse $response): self
    {
        return self::fromArray($response->toArray());
    }
}
