<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/**
 * A RunAPI-owned persona handle, reusable as persona_id in music generation parameters.
 */
readonly class PersonaResource extends BaseModel
{
    /**
     * Create a persona resource value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(public string $id, public ?string $name = null, public ?string $description = null, array $raw = [])
    {
        parent::__construct($raw === [] ? [
            'id' => $id,
            'name' => $name,
            'description' => $description,
        ] : $raw);
    }

    /**
     * Hydrate a persona resource from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            name: Payload::optionalString($raw, 'name'),
            description: Payload::optionalString($raw, 'description'),
            raw: $raw,
        );
    }
}
