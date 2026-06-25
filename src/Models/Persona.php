<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/**
 * Persona response model.
 */
readonly class Persona extends BaseModel
{
    /**
     * Create a persona value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(public string $id, public string $name, public ?string $description = null, array $raw = [])
    {
        parent::__construct($raw === [] ? ['id' => $id, 'name' => $name, 'description' => $description] : $raw);
    }

    /**
     * Hydrate a persona from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::string($raw, 'id'),
            name: Payload::string($raw, 'name'),
            description: self::optionalString($raw, 'description'),
            raw: $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    private static function optionalString(array $raw, string $key): ?string
    {
        $value = $raw[$key] ?? null;

        return is_string($value) ? $value : null;
    }
}
