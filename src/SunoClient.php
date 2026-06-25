<?php

declare(strict_types=1);

namespace RunApi\Suno;

use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\Suno\Resources\AddInstrumental;
use RunApi\Suno\Resources\AddVocals;
use RunApi\Suno\Resources\BoostStyle;
use RunApi\Suno\Resources\CheckVoice;
use RunApi\Suno\Resources\ConvertAudio;
use RunApi\Suno\Resources\CoverAudio;
use RunApi\Suno\Resources\CreateMashup;
use RunApi\Suno\Resources\ExtendMusic;
use RunApi\Suno\Resources\GenerateArtwork;
use RunApi\Suno\Resources\GenerateLyrics;
use RunApi\Suno\Resources\GenerateMidi;
use RunApi\Suno\Resources\GeneratePersona;
use RunApi\Suno\Resources\GenerateVoice;
use RunApi\Suno\Resources\GetTimestampedLyrics;
use RunApi\Suno\Resources\RegenerateValidationPhrase;
use RunApi\Suno\Resources\ReplaceSection;
use RunApi\Suno\Resources\SeparateAudioStems;
use RunApi\Suno\Resources\TextToMusic;
use RunApi\Suno\Resources\TextToSound;
use RunApi\Suno\Resources\VisualizeMusic;
use RunApi\Suno\Resources\VoiceToValidationPhrase;

/**
 * Provides the full Suno music platform: song generation, extension, covers, stems, MIDI, lyrics, mashups, sound effects, visualization, personas, and voice cloning.
 *
 * Exposes typed model resources plus the universal files and account resources.
 */
final class SunoClient extends BaseClient
{
    /**
     * Text to music operations.
     */
    public readonly TextToMusic $textToMusic;
    /**
     * Extend music operations.
     */
    public readonly ExtendMusic $extendMusic;
    /**
     * Generate artwork operations.
     */
    public readonly GenerateArtwork $generateArtwork;
    /**
     * Cover audio operations.
     */
    public readonly CoverAudio $coverAudio;
    /**
     * Add instrumental operations.
     */
    public readonly AddInstrumental $addInstrumental;
    /**
     * Add vocals operations.
     */
    public readonly AddVocals $addVocals;
    /**
     * Separate audio stems operations.
     */
    public readonly SeparateAudioStems $separateAudioStems;
    /**
     * Generate midi operations.
     */
    public readonly GenerateMidi $generateMidi;
    /**
     * Convert audio operations.
     */
    public readonly ConvertAudio $convertAudio;
    /**
     * Visualize music operations.
     */
    public readonly VisualizeMusic $visualizeMusic;
    /**
     * Generate lyrics operations.
     */
    public readonly GenerateLyrics $generateLyrics;
    /**
     * Timestamped lyrics operations.
     */
    public readonly GetTimestampedLyrics $getTimestampedLyrics;
    /**
     * Replace section operations.
     */
    public readonly ReplaceSection $replaceSection;
    /**
     * Create mashup operations.
     */
    public readonly CreateMashup $createMashup;
    /**
     * Text to sound operations.
     */
    public readonly TextToSound $textToSound;
    /**
     * Generate persona operations.
     */
    public readonly GeneratePersona $generatePersona;
    /**
     * Boost style operations.
     */
    public readonly BoostStyle $boostStyle;
    /**
     * Voice to validation phrase operations.
     */
    public readonly VoiceToValidationPhrase $voiceToValidationPhrase;
    /**
     * Regenerate validation phrase operations.
     */
    public readonly RegenerateValidationPhrase $regenerateValidationPhrase;
    /**
     * Generate voice operations.
     */
    public readonly GenerateVoice $generateVoice;
    /**
     * Check voice operations.
     */
    public readonly CheckVoice $checkVoice;

    /**
     * Create a Suno client with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        parent::__construct($options);
        $this->textToMusic = TextToMusic::fromHttp($this->http);
        $this->extendMusic = ExtendMusic::fromHttp($this->http);
        $this->generateArtwork = GenerateArtwork::fromHttp($this->http);
        $this->coverAudio = CoverAudio::fromHttp($this->http);
        $this->addInstrumental = AddInstrumental::fromHttp($this->http);
        $this->addVocals = AddVocals::fromHttp($this->http);
        $this->separateAudioStems = SeparateAudioStems::fromHttp($this->http);
        $this->generateMidi = GenerateMidi::fromHttp($this->http);
        $this->convertAudio = ConvertAudio::fromHttp($this->http);
        $this->visualizeMusic = VisualizeMusic::fromHttp($this->http);
        $this->generateLyrics = GenerateLyrics::fromHttp($this->http);
        $this->getTimestampedLyrics = GetTimestampedLyrics::fromHttp($this->http);
        $this->replaceSection = ReplaceSection::fromHttp($this->http);
        $this->createMashup = CreateMashup::fromHttp($this->http);
        $this->textToSound = TextToSound::fromHttp($this->http);
        $this->generatePersona = GeneratePersona::fromHttp($this->http);
        $this->boostStyle = BoostStyle::fromHttp($this->http);
        $this->voiceToValidationPhrase = VoiceToValidationPhrase::fromHttp($this->http);
        $this->regenerateValidationPhrase = RegenerateValidationPhrase::fromHttp($this->http);
        $this->generateVoice = GenerateVoice::fromHttp($this->http);
        $this->checkVoice = CheckVoice::fromHttp($this->http);
    }
}
