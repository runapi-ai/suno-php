<?php

declare(strict_types=1);

namespace RunApi\Suno\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\Suno\Models\BoostStyleResponse;
use RunApi\Suno\Models\CheckVoiceResponse;
use RunApi\Suno\Models\CompletedAudioTaskResponse;
use RunApi\Suno\Models\GeneratePersonaResponse;
use RunApi\Suno\Models\GetTimestampedLyricsResponse;
use RunApi\Suno\Resources\BoostStyle;
use RunApi\Suno\Resources\CheckVoice;
use RunApi\Suno\Resources\GenerateLyrics;
use RunApi\Suno\Resources\GeneratePersona;
use RunApi\Suno\Resources\GetTimestampedLyrics;
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
        self::assertInstanceOf(CheckVoice::class, $client->checkVoice);
        self::assertInstanceOf(GeneratePersona::class, $client->generatePersona);
        self::assertInstanceOf(GetTimestampedLyrics::class, $client->getTimestampedLyrics);
        self::assertInstanceOf(BoostStyle::class, $client->boostStyle);
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
