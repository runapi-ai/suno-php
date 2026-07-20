<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/** Legacy separated audio fields plus advanced extracted/remaining pairs. */
readonly class SeparatedAudio extends BaseModel
{
    /**
     * @param list<AdvancedStemPair> $pairs
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public ?string $vocalUrl = null,
        public ?string $instrumentalUrl = null,
        public ?string $backingVocalsUrl = null,
        public ?string $bassUrl = null,
        public ?string $brassUrl = null,
        public ?string $drumsUrl = null,
        public ?string $fxUrl = null,
        public ?string $guitarUrl = null,
        public ?string $keyboardUrl = null,
        public ?string $percussionUrl = null,
        public ?string $pianoUrl = null,
        public ?string $stringsUrl = null,
        public ?string $synthUrl = null,
        public ?string $woodwindsUrl = null,
        public array $pairs = [],
        array $raw = [],
    ) {
        $payload = array_filter([
            'vocal_url' => $vocalUrl,
            'instrumental_url' => $instrumentalUrl,
            'backing_vocals_url' => $backingVocalsUrl,
            'bass_url' => $bassUrl,
            'brass_url' => $brassUrl,
            'drums_url' => $drumsUrl,
            'fx_url' => $fxUrl,
            'guitar_url' => $guitarUrl,
            'keyboard_url' => $keyboardUrl,
            'percussion_url' => $percussionUrl,
            'piano_url' => $pianoUrl,
            'strings_url' => $stringsUrl,
            'synth_url' => $synthUrl,
            'woodwinds_url' => $woodwindsUrl,
            'pairs' => array_map(static fn (AdvancedStemPair $pair): array => $pair->toArray(), $pairs),
        ], static fn (mixed $value): bool => $value !== null && $value !== []);

        parent::__construct($raw === [] ? $payload : $raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            vocalUrl: Payload::optionalString($raw, 'vocal_url'),
            instrumentalUrl: Payload::optionalString($raw, 'instrumental_url'),
            backingVocalsUrl: Payload::optionalString($raw, 'backing_vocals_url'),
            bassUrl: Payload::optionalString($raw, 'bass_url'),
            brassUrl: Payload::optionalString($raw, 'brass_url'),
            drumsUrl: Payload::optionalString($raw, 'drums_url'),
            fxUrl: Payload::optionalString($raw, 'fx_url'),
            guitarUrl: Payload::optionalString($raw, 'guitar_url'),
            keyboardUrl: Payload::optionalString($raw, 'keyboard_url'),
            percussionUrl: Payload::optionalString($raw, 'percussion_url'),
            pianoUrl: Payload::optionalString($raw, 'piano_url'),
            stringsUrl: Payload::optionalString($raw, 'strings_url'),
            synthUrl: Payload::optionalString($raw, 'synth_url'),
            woodwindsUrl: Payload::optionalString($raw, 'woodwinds_url'),
            pairs: Payload::listOf($raw, 'pairs', AdvancedStemPair::fromArray(...)),
            raw: $raw,
        );
    }
}
