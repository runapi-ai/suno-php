<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\Models\TaskBillingFacts;
use RunApi\Core\Support\Payload;

/**
 * Result of creating a voice.
 *
 * A request the service completed carries voice. A request it accepted for local
 * execution carries the task acceptance (id and status) instead, and the result
 * is read from the task status endpoint. billing carries the task charge facts
 * (reservation, settlement, refund), not the voice's resource provenance, which
 * the voice resource endpoint reports.
 */
readonly class VoiceCreationResponse extends BaseModel
{
    public ?TaskBillingFacts $billing;

    /**
     * Create a voice creation response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public ?string $id = null,
        public ?string $status = null,
        public ?VoiceResource $voice = null,
        public ?string $error = null,
        array $raw = [],
        ?TaskBillingFacts $billing = null,
    ) {
        $this->billing = $billing ?? self::billing($raw);
        parent::__construct($raw === [] ? [
            'id' => $id,
            'status' => $status,
            'voice' => $voice?->toArray(),
            'error' => $error,
            'billing' => $this->billing?->toArray(),
        ] : $raw);
    }

    /**
     * Hydrate a voice creation response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::optionalString($raw, 'id'),
            status: Payload::optionalString($raw, 'status'),
            voice: self::voice($raw),
            error: Payload::optionalString($raw, 'error'),
            raw: $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    private static function voice(array $raw): ?VoiceResource
    {
        $value = $raw['voice'] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            throw new ValidationException('voice must be an object');
        }

        /** @var array<string, mixed> $value */
        return VoiceResource::fromArray($value);
    }

    /** @param array<string, mixed> $raw */
    private static function billing(array $raw): ?TaskBillingFacts
    {
        return isset($raw['billing']) && is_array($raw['billing']) ? TaskBillingFacts::fromArray($raw['billing']) : null;
    }
}
