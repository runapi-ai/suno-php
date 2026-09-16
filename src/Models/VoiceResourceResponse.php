<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/**
 * Result of retrieving a voice resource: the voice, whether it is ready to use, and the task that produced it.
 */
readonly class VoiceResourceResponse extends BaseModel
{
    /**
     * Create a voice resource response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public VoiceResource $voice,
        public string $status,
        public ResourceBilling $billing,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'voice' => $voice->toArray(),
            'status' => $status,
            'billing' => $billing->toArray(),
        ] : $raw);
    }

    /**
     * Hydrate a voice resource response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            voice: VoiceResource::fromArray(Payload::array($raw, 'voice')),
            status: self::status($raw),
            billing: ResourceBilling::fromArray(Payload::array($raw, 'billing')),
            raw: $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    private static function status(array $raw): string
    {
        $status = Payload::string($raw, 'status');
        if (!in_array($status, ResourceStatus::ALL, true)) {
            throw new ValidationException('status must be one of the allowed values');
        }

        return $status;
    }
}
