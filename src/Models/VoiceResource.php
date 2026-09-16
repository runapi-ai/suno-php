<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/**
 * A RunAPI-owned voice handle, reusable wherever a voice is accepted.
 */
readonly class VoiceResource extends BaseModel
{
    /**
     * Create a voice resource value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(public string $id, public ?string $name = null, array $raw = [])
    {
        parent::__construct($raw === [] ? [
            'id' => $id,
            'name' => $name,
        ] : $raw);
    }

    /**
     * Hydrate a voice resource from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            name: Payload::optionalString($raw, 'name'),
            raw: $raw,
        );
    }
}
