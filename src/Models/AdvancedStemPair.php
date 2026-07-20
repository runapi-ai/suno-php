<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/** Extracted target stem and the audio remaining after removing it. */
readonly class AdvancedStemPair extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        public string $stemName,
        public AdvancedStemAudio $extractedAudio,
        public AdvancedStemAudio $remainingAudio,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'stem_name' => $stemName,
            'extracted_audio' => $extractedAudio->toArray(),
            'remaining_audio' => $remainingAudio->toArray(),
        ] : $raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            stemName: Payload::string($raw, 'stem_name'),
            extractedAudio: AdvancedStemAudio::fromArray(Payload::array($raw, 'extracted_audio')),
            remainingAudio: AdvancedStemAudio::fromArray(Payload::array($raw, 'remaining_audio')),
            raw: $raw,
        );
    }
}
