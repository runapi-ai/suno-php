<?php

declare(strict_types=1);

namespace RunApi\Suno\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\Suno\Models\AudioExportResponse;
use RunApi\Suno\Models\BoostStyleResponse;
use RunApi\Suno\Models\CheckVoiceResponse;
use RunApi\Suno\Models\CompletedAudioExportResponse;
use RunApi\Suno\Models\CompletedAudioTaskResponse;
use RunApi\Suno\Models\CompletedLyricsTaskResponse;
use RunApi\Suno\Models\CompletedMusicFromSampleResponse;
use RunApi\Suno\Models\CompletedMusicVisualizationResponse;
use RunApi\Suno\Models\GeneratePersonaResponse;
use RunApi\Suno\Models\GetTimestampedLyricsResponse;
use RunApi\Suno\Models\PersonaCreationResponse;
use RunApi\Suno\Models\PersonaResourceResponse;
use RunApi\Suno\Models\ResourceStatus;
use RunApi\Suno\Models\SeparateAudioStemsResponse;
use RunApi\Suno\Models\VoiceCreationResponse;
use RunApi\Suno\Models\VoiceResourceResponse;
use RunApi\Suno\Resources\AddSamples;
use RunApi\Suno\Resources\AudioExports;
use RunApi\Suno\Resources\BlendLyrics;
use RunApi\Suno\Resources\BoostStyle;
use RunApi\Suno\Resources\CheckVoice;
use RunApi\Suno\Resources\GenerateLyrics;
use RunApi\Suno\Resources\GeneratePersona;
use RunApi\Suno\Resources\GetTimestampedLyrics;
use RunApi\Suno\Resources\InspireMusic;
use RunApi\Suno\Resources\MusicFromSample;
use RunApi\Suno\Resources\MusicVisualizations;
use RunApi\Suno\Resources\Personas;
use RunApi\Suno\Resources\StyleExpansions;
use RunApi\Suno\Resources\SyncResource;
use RunApi\Suno\Resources\TextToMusic;
use RunApi\Suno\Resources\TimestampedLyrics;
use RunApi\Suno\Resources\Voices;
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
        self::assertInstanceOf(Personas::class, $client->personas);
        self::assertInstanceOf(Voices::class, $client->voices);
        self::assertInstanceOf(StyleExpansions::class, $client->styleExpansions);
        self::assertInstanceOf(TimestampedLyrics::class, $client->timestampedLyrics);
        self::assertInstanceOf(AudioExports::class, $client->audioExports);
        self::assertInstanceOf(MusicVisualizations::class, $client->musicVisualizations);
        self::assertInstanceOf(MusicFromSample::class, $client->musicFromSample);
    }

    public function testAudioActionsPostPublicRequestShapes(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"stitch","status":"processing"}'),
            new Response(200, [], '{"id":"remaster","status":"processing"}'),
            new Response(200, [], '{"id":"samples","status":"processing"}'),
            new Response(200, [], '{"id":"inspiration","status":"processing"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));
        $owned = ['model' => 'suno-v5', 'source_task_id' => 'source', 'audio_id' => 'audio'];
        $samples = ['model' => 'suno-v5', 'audio_url' => 'https://file.runapi.ai/source.mp3', 'prompt' => 'Add a crisp handclap sample to the chorus', 'start_seconds' => 5, 'end_seconds' => 20];
        $inspiration = [
            'model' => 'suno-v5',
            'audio_urls' => ['https://file.runapi.ai/inspiration-one.mp3', 'https://file.runapi.ai/inspiration-two.mp3']];

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
            'start_seconds' => 20, 'end_seconds' => 20]);
    }

    public function testTextToMusicAndGenerateLyricsCreate(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"song_task","status":"processing"}'),
            new Response(200, [], '{"id":"lyrics_task","status":"processing"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertSame('song_task', $client->textToMusic->create([
            'model' => 'suno-v5.5',
            'prompt' => 'A chill lo-fi beat',
            'vocal_mode' => 'auto_lyrics'])->id);
        self::assertSame('lyrics_task', $client->generateLyrics->create([
            'prompt' => 'A chorus about sunrise'])->id);

        self::assertSame('/api/v1/suno/text_to_music', $transport->requests[0]->getUri()->getPath());
        self::assertSame('/api/v1/suno/generate_lyrics', $transport->requests[1]->getUri()->getPath());
    }

    public function testBlendLyricsCreateAndRunUseTypedLyricsResponses(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"blend_task","status":"processing"}'),
            new Response(200, [], '{"id":"blend_task_run","status":"processing"}'),
            new Response(200, [], '{"id":"blend_task_run","status":"completed","lyrics":[{"title":"Mashup","text":"Blended lyrics"}],"usage":{"cost":0.05}}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertSame('blend_task', $client->blendLyrics->create([
            'lyrics_a' => 'First verse',
            'lyrics_b' => 'Second verse'])->id);
        $result = $client->blendLyrics->run([
            'lyrics_a' => 'First verse',
            'lyrics_b' => 'Second verse'], new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 1.0));

        self::assertInstanceOf(CompletedLyricsTaskResponse::class, $result);
        self::assertSame('Blended lyrics', $result->lyrics[0]->text);
        self::assertSame('/api/v1/suno/blend_lyrics', $transport->requests[0]->getUri()->getPath());
    }

    public function testReplaceSectionSupportsUploadedAudioSource(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"section_task","status":"processing"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertSame('section_task', $client->replaceSection->create([
            'upload_url' => 'https://cdn.runapi.ai/public/samples/music.mp3',
            'model' => 'suno-v5.5',
            'lyrics' => 'solo',
            'full_lyrics' => '[Verse] solo',
            'tags' => 'rock',
            'title' => 'Song',
            'infill_start_time' => 10.0,
            'infill_end_time' => 20.0])->id);

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
            new Response(200, [], '{"id":"stems_task","status":"processing"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        self::assertSame('stems_task', $client->separateAudioStems->create([
            'task_id' => 'task_source',
            'audio_id' => 'audio_source',
            'type' => 'split_stem_advanced',
            'stem_name' => 'Bass'])->id);

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
                'type' => 'split_stem_advanced']);
        } finally {
            self::assertSame([], $transport->requests);
        }
    }

    public function testSeparateAudioStemsAdvancedDecodesTypedPair(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"stems_task","status":"completed", "usage": {"cost": 0.05},"separated_audios":{"pairs":[{"stem_name":"Bass","extracted_audio":{"id":"audio_bass","duration_seconds":116.28,"audio_url":"https://file.runapi.ai/bass.mp3"},"remaining_audio":{"id":"audio_without_bass","duration_seconds":116.28,"audio_url":"https://file.runapi.ai/without-bass.mp3"}}]},"usage":{"cost":0.05}}')]);
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
            new Response(200, [], '{"id":"stems_task","status":"completed","separated_audios":{"vocal_url":"https://file.runapi.ai/vocal.mp3","instrumental_url":"https://file.runapi.ai/instrumental.mp3"},"usage":{"cost":0.05}}')]);
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
                'infill_end_time' => 20.0]);
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
                'infill_end_time' => $endTime]);
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
            'infill_end_time' => 71.0]);

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
            'infill_end_time' => 16.016]);

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
            'infill_end_time' => INF]);
    }

    public function testTextToMusicRunReturnsTypedCompletedResponse(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"song_task","status":"processing"}'),
            new Response(200, [], '{"id":"song_task","status":"completed","usage":{"cost":0.05}}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->textToMusic->run([
            'model' => 'suno-v5.5',
            'prompt' => 'A chill lo-fi beat',
            'vocal_mode' => 'auto_lyrics']);

        self::assertInstanceOf(CompletedAudioTaskResponse::class, $result);
        self::assertSame('completed', $result->status);
    }

    public function testSynchronousResourcesReturnTypedResponsesWithoutPolling(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"is_available":true,"extra_field":"kept"}'),
            new Response(200, [], '{"persona":{"id":"persona_1","name":"Narrator","description":"warm"},"extra_field":"kept"}'),
            new Response(200, [], '{"aligned_words":[{"word":"hello","success":true,"start_time":0,"end_time":0.5,"palign":0.98}],"waveform_data":[0,0.5,1],"hoot_cer":0.1,"is_streamed":false,"extra_field":"kept"}'),
            new Response(200, [], '{"style":"dream pop, soft drums","extra_field":"kept"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $check = $client->checkVoice->run(['task_id' => 'voice_task']);
        $persona = $client->generatePersona->run([
            'task_id' => 'song_task',
            'audio_id' => 'audio_1',
            'name' => 'Narrator',
            'description' => 'warm']);
        $lyrics = $client->getTimestampedLyrics->run(['task_id' => 'song_task', 'audio_id' => 'audio_1']);
        $style = $client->boostStyle->run([
            'description' => 'dreamy pop',
            'name' => 'unused',
            'callback_url' => '']);

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

    public function testPersonasCreateAndReadRunAPIResource(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"persona":{"id":"res_persona","name":"Narrator","description":"warm"}}'),
            new Response(200, [], '{"persona":{"id":"res_persona","name":"Narrator","description":"warm"},"status":"available"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $created = $client->personas->run([
            'source_task_id' => 'song_task',
            'source_audio_id' => 'audio_1',
            'name' => 'Narrator',
            'description' => 'warm']);
        $resource = $client->personas->get('res_persona');

        self::assertInstanceOf(PersonaCreationResponse::class, $created);
        self::assertNotNull($created->persona);
        self::assertSame('res_persona', $created->persona->id);
        self::assertSame('Narrator', $created->persona->name);
        self::assertSame('warm', $created->persona->description);

        self::assertInstanceOf(PersonaResourceResponse::class, $resource);
        self::assertSame('res_persona', $resource->persona->id);
        self::assertSame('Narrator', $resource->persona->name);
        self::assertSame(ResourceStatus::AVAILABLE, $resource->status);

        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/personas', $transport->requests[0]->getUri()->getPath());
        self::assertSame([
            'source_task_id' => 'song_task',
            'source_audio_id' => 'audio_1',
            'name' => 'Narrator',
            'description' => 'warm'], json_decode((string) $transport->requests[0]->getBody(), true, 512, JSON_THROW_ON_ERROR));
        self::assertSame('GET', $transport->requests[1]->getMethod());
        self::assertSame('/api/v1/personas/res_persona', $transport->requests[1]->getUri()->getPath());
    }

    public function testPersonasFollowAnAcceptedTaskToItsStoredPersona(): void
    {
        $transport = new QueueHttpClient([
            new Response(202, ['Location' => '/api/v1/tasks/local_persona'], '{"id":"local_persona","status":"pending"}'),
            new Response(200, [], '{"id":"local_persona","status":"completed","response":{"status":200,"content_type":"application/json","body":{"persona":{"id":"res_local","name":"Local"}}},"usage":{"cost":0.05}}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $created = $client->personas->run([
            'source_task_id' => 'song_task',
            'source_audio_id' => 'audio_1',
            'name' => 'Local',
            'description' => 'queued for local execution'], new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 1.0));

        self::assertInstanceOf(PersonaCreationResponse::class, $created);
        self::assertSame('res_local', $created->persona?->id);
        self::assertSame('/api/v1/tasks/local_persona', $transport->requests[1]->getUri()->getPath());
    }

    public function testVoicesCreateAndReadRunAPIResource(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"voice":{"id":"res_voice","name":"Studio Voice"},"status":"completed", "usage": {"cost": 0.05}}'),
            new Response(200, [], '{"voice":{"id":"res_voice","name":"Studio Voice"},"status":"available"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $created = $client->voices->run([
            'source_audio_url' => 'https://file.runapi.ai/voice.mp3',
            'name' => 'Studio Voice']);
        $resource = $client->voices->get('res_voice');

        self::assertInstanceOf(VoiceCreationResponse::class, $created);
        self::assertNotNull($created->voice);
        self::assertSame('res_voice', $created->voice->id);
        self::assertSame('Studio Voice', $created->voice->name);

        self::assertInstanceOf(VoiceResourceResponse::class, $resource);
        self::assertSame('res_voice', $resource->voice->id);
        self::assertSame(ResourceStatus::AVAILABLE, $resource->status);

        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/voices', $transport->requests[0]->getUri()->getPath());
        self::assertSame([
            'source_audio_url' => 'https://file.runapi.ai/voice.mp3',
            'name' => 'Studio Voice'], json_decode((string) $transport->requests[0]->getBody(), true, 512, JSON_THROW_ON_ERROR));
        self::assertSame('GET', $transport->requests[1]->getMethod());
        self::assertSame('/api/v1/voices/res_voice', $transport->requests[1]->getUri()->getPath());
    }

    public function testStyleExpansionsExpandADescription(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"style":"dream pop, soft drums"}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $style = $client->styleExpansions->run(['description' => 'dreamy pop']);

        self::assertInstanceOf(BoostStyleResponse::class, $style);
        self::assertSame('dream pop, soft drums', $style->style);

        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/style_expansions', $transport->requests[0]->getUri()->getPath());
        self::assertSame(['description' => 'dreamy pop'], json_decode((string) $transport->requests[0]->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testTimestampedLyricsAlignAnAudioResource(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"aligned_words":[{"word":"hello","success":true,"start_time":0,"end_time":0.5,"palign":0.98}],"waveform_data":[0,0.5,1],"hoot_cer":0.02,"is_streamed":false}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $lyrics = $client->timestampedLyrics->run([
            'source_audio_id' => 'res_audio',
            'source_task_id' => 'song_task']);

        self::assertInstanceOf(GetTimestampedLyricsResponse::class, $lyrics);
        self::assertSame('hello', $lyrics->alignedWords[0]->word);
        self::assertSame(0.5, $lyrics->waveformData[1]);
        self::assertSame(0.02, $lyrics->hootCer);
        self::assertFalse($lyrics->isStreamed);

        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/timestamped_lyrics', $transport->requests[0]->getUri()->getPath());
        self::assertSame([
            'source_audio_id' => 'res_audio',
            'source_task_id' => 'song_task'], json_decode((string) $transport->requests[0]->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testAudioExportsCreatePollAndRun(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"export_task","status":"processing"}'),
            new Response(200, [], '{"id":"export_task","status":"processing"}'),
            new Response(200, [], '{"id":"export_task","status":"processing"}'),
            new Response(200, [], '{"id":"export_task","status":"completed","wav_url":"https://file.runapi.ai/track.wav","original_task_id":"song_task","usage":{"cost":0.05}}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));
        $params = ['source_audio_id' => 'res_audio'];

        $created = $client->audioExports->create($params);
        $polled = $client->audioExports->get('export_task');
        $completed = $client->audioExports->run($params, new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 1.0));

        self::assertInstanceOf(TaskCreateResponse::class, $created);
        self::assertSame('export_task', $created->id);
        self::assertInstanceOf(AudioExportResponse::class, $polled);
        self::assertSame('processing', $polled->status);
        self::assertNull($polled->wavUrl);
        self::assertInstanceOf(CompletedAudioExportResponse::class, $completed);
        self::assertSame('https://file.runapi.ai/track.wav', $completed->wavUrl);
        self::assertSame('song_task', $completed->originalTaskId);
        self::assertSame(0.05, $completed->usage?->cost);

        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/audio_exports', $transport->requests[0]->getUri()->getPath());
        self::assertSame($params, json_decode((string) $transport->requests[0]->getBody(), true, 512, JSON_THROW_ON_ERROR));
        self::assertSame('GET', $transport->requests[1]->getMethod());
        self::assertSame('/api/v1/audio_exports/export_task', $transport->requests[1]->getUri()->getPath());
        self::assertSame('/api/v1/audio_exports/export_task', $transport->requests[3]->getUri()->getPath());
    }

    public function testMusicVisualizationsCreatePollAndRun(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"visual_task","status":"processing"}'),
            new Response(200, [], '{"id":"visual_task","status":"processing"}'),
            new Response(200, [], '{"id":"visual_task","status":"completed","video_url":"https://file.runapi.ai/visual.mp4","original_task_id":"song_task","usage":{"cost":0.05}}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));
        $params = [
            'source_audio_id' => 'res_audio',
            'author' => 'Release Day',
            'domain_name' => 'runapi.ai'];

        $created = $client->musicVisualizations->create($params);
        $completed = $client->musicVisualizations->run($params, new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 1.0));

        self::assertSame('visual_task', $created->id);
        self::assertInstanceOf(CompletedMusicVisualizationResponse::class, $completed);
        self::assertSame('https://file.runapi.ai/visual.mp4', $completed->videoUrl);
        self::assertSame('song_task', $completed->originalTaskId);

        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/music_visualizations', $transport->requests[0]->getUri()->getPath());
        self::assertSame($params, json_decode((string) $transport->requests[0]->getBody(), true, 512, JSON_THROW_ON_ERROR));
        self::assertSame('GET', $transport->requests[2]->getMethod());
        self::assertSame('/api/v1/music_visualizations/visual_task', $transport->requests[2]->getUri()->getPath());
    }

    public function testMusicFromSampleCreatePollAndRun(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"sample_task","status":"processing"}'),
            new Response(200, [], '{"id":"sample_task","status":"processing"}'),
            new Response(200, [], '{"id":"sample_task","status":"completed","audios":[{"id":"audio_1","audio_url":"https://file.runapi.ai/sample.mp3"}],"usage":{"cost":0.05}}')]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));
        $params = [
            'model' => 'suno-v5.5',
            'audio_url' => 'https://file.runapi.ai/source.mp3',
            'prompt' => 'Add a crisp handclap sample to the chorus',
            'start_seconds' => 5,
            'end_seconds' => 20];

        $created = $client->musicFromSample->create($params);
        $completed = $client->musicFromSample->run($params, new RequestOptions(pollIntervalSeconds: 0.0, maxWaitSeconds: 1.0));

        self::assertSame('sample_task', $created->id);
        self::assertInstanceOf(CompletedMusicFromSampleResponse::class, $completed);
        self::assertSame('https://file.runapi.ai/sample.mp3', $completed->audios[0]['audio_url']);

        self::assertSame('POST', $transport->requests[0]->getMethod());
        self::assertSame('/api/v1/music_from_sample', $transport->requests[0]->getUri()->getPath());
        self::assertSame($params, json_decode((string) $transport->requests[0]->getBody(), true, 512, JSON_THROW_ON_ERROR));
        self::assertSame('GET', $transport->requests[2]->getMethod());
        self::assertSame('/api/v1/music_from_sample/sample_task', $transport->requests[2]->getUri()->getPath());
    }

    public function testMusicFromSampleRejectsInvalidWindowBeforeRequest(): void
    {
        $transport = new QueueHttpClient([]);
        $client = new SunoClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('end_seconds must be greater than start_seconds');

        try {
            $client->musicFromSample->create([
                'model' => 'suno-v5.5',
                'audio_url' => 'https://file.runapi.ai/source.mp3',
                'start_seconds' => 20,
                'end_seconds' => 20]);
        } finally {
            self::assertSame([], $transport->requests);
        }
    }

    private function runWithoutRequiredFields(SyncResource $resource): void
    {
        $resource->run([]);
    }
}
