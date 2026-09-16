<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Support\Payload;

/**
 * Completed music visualization returned by run(); the rendered video is guaranteed present.
 */
readonly class CompletedMusicVisualizationResponse extends CompletedAudioTaskResponse
{
    /**
     * Create a completed music visualization response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        ?string $id,
        string $status,
        ?string $error = null,
        public ?string $videoUrl = null,
        public ?string $originalTaskId = null,
        array $raw = [],
    ) {
        parent::__construct(id: $id, status: $status, error: $error, raw: $raw);
    }

    /**
     * Hydrate a completed music visualization response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            status: Payload::string($raw, 'status'),
            error: self::error($raw),
            videoUrl: Payload::optionalString($raw, 'video_url'),
            originalTaskId: Payload::optionalString($raw, 'original_task_id'),
            raw: $raw,
        );
    }

    /**
     * Narrow a polled task response after completion has been confirmed.
     */
    public static function fromResponse(AudioTaskResponse $response): self
    {
        return self::fromArray($response->toArray());
    }
}
