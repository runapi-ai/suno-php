<?php

declare(strict_types=1);

namespace RunApi\Suno\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\RequestOptions;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\Suno\Models\BoostStyleResponse;
use RunApi\Suno\Models\CheckVoiceResponse;
use RunApi\Suno\Models\CompletedAudioTaskResponse;
use RunApi\Suno\Models\CompletedLyricsTaskResponse;
use RunApi\Suno\Models\GeneratePersonaResponse;
use RunApi\Suno\Models\GetTimestampedLyricsResponse;
use RunApi\Suno\Models\SeparateAudioStemsResponse;
use RunApi\Suno\Resources\AddSamples;
use RunApi\Suno\Resources\BlendLyrics;
use RunApi\Suno\Resources\BoostStyle;
use RunApi\Suno\Resources\CheckVoice;
use RunApi\Suno\Resources\GenerateLyrics;
use RunApi\Suno\Resources\GeneratePersona;
use RunApi\Suno\Resources\GetTimestampedLyrics;
use RunApi\Suno\Resources\InspireMusic;
use RunApi\Suno\Resources\SyncResource;
use RunApi\Suno\Resources\TextToMusic;
use RunApi\Suno\SunoClient;

final class SunoClientTest extends TestCase
{
    public function testExposesTypedResources(): void
    {
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        self::assertInstanceOf(TextToMusic::class, $client->textToMusic);
        self::assertInstanceOf(GenerateLyrics::class, $client->generateLyrics);
        self::assertInstanceOf(BlendLyrics::class, $client->blendLyrics);
        self::assertInstanceOf(CheckVoice::class, $client->checkVoice);
        self::assertInstanceOf(GeneratePersona::class, $client->generatePersona);
        self::assertInstanceOf(GetTimestampedLyrics::class, $client->getTimestampedLyrics);
        self::assertInstanceOf(BoostStyle::class, $client->boostStyle);
        self::assertInstanceOf(AddSamples::class, $client->addSamples);
        self::assertInstanceOf(InspireMusic::class, $client->inspireMusic);
    }

    public function testAudioActionsPostPublicRequestShapes(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"stitch","status":"processing"}'),
            new Response(200, [], '{"id":"remaster","status":"processing"}'),
            new Response(200, [], '{"id":"samples","status":"processing"}'),
            new Response(200, [], '{"id":"inspiration","status":"processing"}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));
        $owned = ['model' => 'suno-v5', 'source_task_id' => 'source', 'audio_id' => 'audio'];
        $samples = ['model' => 'suno-v5', 'audio_url' => 'https://file.runapi.ai/source.mp3', 'start_seconds' => 5, 'end_seconds' => 20];
        $inspiration = [
            'model' => 'suno-v5',
            'audio_urls' => ['https://file.runapi.ai/inspiration-one.mp3', 'https://file.runapi.ai/inspiration-two.mp3'],
        ];

        $client->stitchAudio->create($owned);
        $client->remasterAudio->create($owned);
        $client->addSamples->create($samples);
        $client->inspireMusic->create($inspiration);

        self::assertSame('/api/v1/suno/stitch_audio', $transport->requests[0]->getUri()->getPath());
        self::assertSame('/api/v1/suno/remaster_audio', $transport->requests[1]->getUri()->getPath());
        self::assertSame('/api/v1/suno/add_samples', $transport->requests[2]->getUri()->getPath());
        self::assertSame($samples, json_decode((string) $transport->requests[2]->getBody(), true, 512, JSON_THROW_ON_ERROR));
        self::assertSame('/api/v1/suno/inspire_music', $transport->requests[3]->getUri()->getPath());
        $inspirationBody = json_decode((string) $transport->requests[3]->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame($inspiration, $inspirationBody);
        self::assertArrayNotHasKey('audio_id', $inspirationBody);
    }

    public function testAddSamplesRejectsInvalidWindow(): void
    {
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('end_seconds must be greater than start_seconds');
        $client->addSamples->create([
            'model' => 'suno-v5', 'audio_url' => 'https://file.runapi.ai/source.mp3',
            'start_seconds' => 20, 'end_seconds' => 20,
        ]);
    }

    public function testTextToMusicAndGenerateLyricsCreate(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"song_task","status":"processing"}'),
            new Response(200, [], '{"id":"lyrics_task","status":"processing"}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertSame('song_task', $client->textToMusic->create([
            'model' => 'suno-v5.5',
            'prompt' => 'A chill lo-fi beat',
            'vocal_mode' => 'auto_lyrics',
        ])->id);
        self::assertSame('lyrics_task', $client->generateLyrics->create([
            'prompt' => 'A chorus about sunrise',
        ])->id);

        self::assertSame('/api/v1/suno/text_to_music', $transport->requests[0]->getUri()->getPath());
        self::assertSame('/api/v1/suno/generate_lyrics', $transport->requests[1]->getUri()->getPath());
    }

    public function testBlendLyricsCreateAndRunUseTypedLyricsResponses(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"blend_task","status":"processing"}'),
            new Response(200, [], '{"id":"blend_task_run","status":"processing"}'),
            new Response(200, [], '{"id":"blend_task_run","status":"completed","lyrics":[{"title":"Mashup","text":"Blended lyrics"}]}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertSame('blend_task', $client->blendLyrics->create([
            'lyrics_a' => 'First verse',
            'lyrics_b' => 'Second verse',
        ])->id);
        $result = $client->blendLyrics->run([
            'lyrics_a' => 'First verse',
            'lyrics_b' => 'Second verse',
        ], new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 1.0));

        self::assertInstanceOf(CompletedLyricsTaskResponse::class, $result);
        self::assertSame('Blended lyrics', $result->lyrics[0]->text);
        self::assertSame('/api/v1/suno/blend_lyrics', $transport->requests[0]->getUri()->getPath());
    }

    public function testReplaceSectionSupportsUploadedAudioSource(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"section_task","status":"processing"}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertSame('section_task', $client->replaceSection->create([
            'upload_url' => 'https://cdn.runapi.ai/public/samples/music.mp3',
            'model' => 'suno-v5.5',
            'lyrics' => 'solo',
            'full_lyrics' => '[Verse] solo',
            'tags' => 'rock',
            'title' => 'Song',
            'infill_start_time' => 10.0,
            'infill_end_time' => 20.0,
        ])->id);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('/api/v1/suno/replace_section', $transport->requests[0]->getUri()->getPath());
        self::assertSame('https://cdn.runapi.ai/public/samples/music.mp3', $body['upload_url']);
        self::assertSame('suno-v5.5', $body['model']);
        self::assertArrayNotHasKey('task_id', $body);
        self::assertArrayNotHasKey('audio_id', $body);
    }

    public function testSeparateAudioStemsAdvancedUsesCanonicalStemName(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"stems_task","status":"processing"}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertSame('stems_task', $client->separateAudioStems->create([
            'task_id' => 'task_source',
            'audio_id' => 'audio_source',
            'type' => 'split_stem_advanced',
            'stem_name' => 'Bass',
        ])->id);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('split_stem_advanced', $body['type']);
        self::assertSame('Bass', $body['stem_name']);
        self::assertArrayNotHasKey('stemName', $body);
    }

    public function testSeparateAudioStemsAdvancedRequiresStemNameBeforeRequest(): void
    {
        $transport = new QueueHttpClient([]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('stem_name is required when type is split_stem_advanced');

        try {
            $client->separateAudioStems->create([
                'task_id' => 'task_source',
                'audio_id' => 'audio_source',
                'type' => 'split_stem_advanced',
            ]);
        } finally {
            self::assertSame([], $transport->requests);
        }
    }

    public function testSeparateAudioStemsAdvancedDecodesTypedPair(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"stems_task","status":"completed","separated_audios":{"pairs":[{"stem_name":"Bass","extracted_audio":{"id":"audio_bass","duration_seconds":116.28,"audio_url":"https://file.runapi.ai/bass.mp3"},"remaining_audio":{"id":"audio_without_bass","duration_seconds":116.28,"audio_url":"https://file.runapi.ai/without-bass.mp3"}}]}}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $response = $client->separateAudioStems->get('stems_task');

        self::assertInstanceOf(SeparateAudioStemsResponse::class, $response);
        self::assertNotNull($response->separatedAudios);
        self::assertSame('Bass', $response->separatedAudios->pairs[0]->stemName);
        self::assertSame('audio_bass', $response->separatedAudios->pairs[0]->extractedAudio->id);
        self::assertSame('https://file.runapi.ai/without-bass.mp3', $response->separatedAudios->pairs[0]->remainingAudio->audioUrl);
    }

    public function testSeparateAudioStemsDecodesLegacyUrls(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"stems_task","status":"completed","separated_audios":{"vocal_url":"https://file.runapi.ai/vocal.mp3","instrumental_url":"https://file.runapi.ai/instrumental.mp3"}}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $response = $client->separateAudioStems->get('stems_task');

        self::assertNotNull($response->separatedAudios);
        self::assertSame('https://file.runapi.ai/vocal.mp3', $response->separatedAudios->vocalUrl);
        self::assertSame('https://file.runapi.ai/instrumental.mp3', $response->separatedAudios->instrumentalUrl);
        self::assertSame([], $response->separatedAudios->pairs);
    }

    public function testReplaceSectionRejectsMixedSourcesBeforeRequest(): void
    {
        $transport = new QueueHttpClient([]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('task_id/audio_id cannot be combined with upload_url/model');

        try {
            $client->replaceSection->create([
                'task_id' => 'task_1',
                'audio_id' => 'audio_1',
                'upload_url' => 'https://cdn.runapi.ai/public/samples/music.mp3',
                'model' => 'suno-v5.5',
                'lyrics' => 'solo',
                'full_lyrics' => '[Verse] solo',
                'tags' => 'rock',
                'title' => 'Song',
                'infill_start_time' => 10.0,
                'infill_end_time' => 20.0,
            ]);
        } finally {
            self::assertSame([], $transport->requests);
        }
    }

    /**
     * @dataProvider invalidReplaceSectionTimeWindows
     */
    public function testReplaceSectionRejectsInvalidTimeWindow(float $startTime, float $endTime, string $message): void
    {
        $transport = new QueueHttpClient([]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($message);

        try {
            $client->replaceSection->create([
                'task_id' => 'task_1',
                'audio_id' => 'audio_1',
                'lyrics' => 'solo',
                'full_lyrics' => '[Verse] solo',
                'tags' => 'rock',
                'title' => 'Song',
                'infill_start_time' => $startTime,
                'infill_end_time' => $endTime,
            ]);
        } finally {
            self::assertSame([], $transport->requests);
        }
    }

    /**
     * @return iterable<string, array{float, float, string}>
     */
    public static function invalidReplaceSectionTimeWindows(): iterable
    {
        yield 'end before start' => [10.0, 5.0, 'infill_end_time must be greater than infill_start_time'];
        yield 'duration too short' => [10.0, 19.999, 'replacement duration must be at least 10 seconds'];
    }

    public function testReplaceSectionAcceptsDurationLongerThanSixtySeconds(): void
    {
        $transport = new QueueHttpClient([new Response(200, [], '{"id":"task_1","status":"processing"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->replaceSection->create([
            'task_id' => 'task_1',
            'audio_id' => 'audio_1',
            'lyrics' => 'solo',
            'full_lyrics' => '[Verse] solo',
            'tags' => 'rock',
            'title' => 'Song',
            'infill_start_time' => 10.0,
            'infill_end_time' => 71.0,
        ]);

        self::assertCount(1, $transport->requests);
    }

    public function testReplaceSectionAcceptsDecimalDurationOfExactlyTenSeconds(): void
    {
        $transport = new QueueHttpClient([new Response(200, [], '{"id":"task_1","status":"processing"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->replaceSection->create([
            'task_id' => 'task_1',
            'audio_id' => 'audio_1',
            'lyrics' => 'solo',
            'full_lyrics' => '[Verse] solo',
            'tags' => 'rock',
            'title' => 'Song',
            'infill_start_time' => 6.016,
            'infill_end_time' => 16.016,
        ]);

        self::assertCount(1, $transport->requests);
    }

    public function testReplaceSectionRejectsNonFiniteTimes(): void
    {
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('infill_end_time must be a finite number');
        $client->replaceSection->create([
            'task_id' => 'task_1',
            'audio_id' => 'audio_1',
            'lyrics' => 'solo',
            'full_lyrics' => '[Verse] solo',
            'tags' => 'rock',
            'title' => 'Song',
            'infill_start_time' => 0.0,
            'infill_end_time' => INF,
        ]);
    }

    public function testTextToMusicRunReturnsTypedCompletedResponse(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"song_task","status":"processing"}'),
            new Response(200, [], '{"id":"song_task","status":"completed"}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->textToMusic->run([
            'model' => 'suno-v5.5',
            'prompt' => 'A chill lo-fi beat',
            'vocal_mode' => 'auto_lyrics',
        ]);

        self::assertInstanceOf(CompletedAudioTaskResponse::class, $result);
        self::assertSame('completed', $result->status);
    }

    public function testSynchronousResourcesReturnTypedResponsesWithoutPolling(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"is_available":true,"extra_field":"kept"}'),
            new Response(200, [], '{"persona":{"id":"persona_1","name":"Narrator","description":"warm"},"extra_field":"kept"}'),
            new Response(200, [], '{"aligned_words":[{"word":"hello","success":true,"start_time":0,"end_time":0.5,"palign":0.98}],"waveform_data":[0,0.5,1],"hoot_cer":0.1,"is_streamed":false,"extra_field":"kept"}'),
            new Response(200, [], '{"style":"dream pop, soft drums","extra_field":"kept"}'),
        ]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $check = $client->checkVoice->run(['task_id' => 'voice_task']);
        $persona = $client->generatePersona->run([
            'task_id' => 'song_task',
            'audio_id' => 'audio_1',
            'name' => 'Narrator',
            'description' => 'warm',
        ]);
        $lyrics = $client->getTimestampedLyrics->run(['task_id' => 'song_task', 'audio_id' => 'audio_1']);
        $style = $client->boostStyle->run([
            'description' => 'dreamy pop',
            'name' => 'unused',
            'callback_url' => '',
        ]);

        self::assertInstanceOf(CheckVoiceResponse::class, $check);
        self::assertTrue($check->isAvailable);
        self::assertSame('kept', $check->toArray()['extra_field']);

        self::assertInstanceOf(GeneratePersonaResponse::class, $persona);
        self::assertSame('persona_1', $persona->persona->id);
        self::assertSame('Narrator', $persona->persona->name);
        self::assertSame('kept', $persona->toArray()['extra_field']);

        self::assertInstanceOf(GetTimestampedLyricsResponse::class, $lyrics);
        self::assertSame('hello', $lyrics->alignedWords[0]->word);
        self::assertSame(0.5, $lyrics->waveformData[1]);
        self::assertFalse($lyrics->isStreamed);

        self::assertInstanceOf(BoostStyleResponse::class, $style);
        self::assertSame('dream pop, soft drums', $style->style);

        self::assertCount(4, $transport->requests);
        self::assertSame('/api/v1/suno/check_voice', $transport->requests[0]->getUri()->getPath());
        self::assertSame('/api/v1/suno/generate_persona', $transport->requests[1]->getUri()->getPath());
        self::assertSame('/api/v1/suno/get_timestamped_lyrics', $transport->requests[2]->getUri()->getPath());
        self::assertSame('/api/v1/suno/boost_style', $transport->requests[3]->getUri()->getPath());
    }

    public function testSynchronousResourcesRejectMissingRequiredFieldsBeforeRequest(): void
    {
        $transport = new QueueHttpClient([]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('task_id is required');

        try {
            $this->runWithoutRequiredFields($client->checkVoice);
        } finally {
            self::assertSame([], $transport->requests);
        }
    }

    private function runWithoutRequiredFields(SyncResource $resource): void
    {
        $resource->run([]);
    }
}
