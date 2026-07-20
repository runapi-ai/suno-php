<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Support\Payload;

/** Stem-separation task response with legacy URLs or advanced audio pairs. */
readonly class SeparateAudioStemsResponse extends AudioTaskResponse
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        ?string $id,
        string $status,
        ?string $error,
        public ?SeparatedAudio $separatedAudios,
        array $raw = [],
    ) {
        parent::__construct(id: $id, status: $status, error: $error, raw: $raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        $separatedAudios = array_key_exists('separated_audios', $raw)
            ? SeparatedAudio::fromArray(Payload::array($raw, 'separated_audios'))
            : null;

        return new self(
            id: Payload::optionalString($raw, 'id'),
            status: Payload::string($raw, 'status'),
            error: self::error($raw),
            separatedAudios: $separatedAudios,
            raw: $raw,
        );
    }
}
