<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Support\Payload;

/**
 * Completed audio export returned by run(); the exported file is guaranteed present.
 */
readonly class CompletedAudioExportResponse extends CompletedAudioTaskResponse
{
    /**
     * Create a completed audio export response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        ?string $id,
        string $status,
        ?string $error = null,
        public ?string $wavUrl = null,
        public ?string $originalTaskId = null,
        array $raw = [],
    ) {
        parent::__construct(id: $id, status: $status, error: $error, raw: $raw);
    }

    /**
     * Hydrate a completed audio export response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            status: Payload::string($raw, 'status'),
            error: self::error($raw),
            wavUrl: Payload::optionalString($raw, 'wav_url'),
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
