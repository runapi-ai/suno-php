<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/**
 * Async persona task response with lifecycle status and output files.
 */
readonly class GeneratePersonaResponse extends BaseModel
{
    /**
     * Create a persona generation response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(public Persona $persona, public ?string $error = null, array $raw = [])
    {
        parent::__construct($raw === [] ? ['persona' => $persona->toArray(), 'error' => $error] : $raw);
    }

    /**
     * Hydrate a persona generation response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(persona: Persona::fromArray(Payload::array($raw, 'persona')), error: self::error($raw), raw: $raw);
    }

    /** @param array<string, mixed> $raw */
    private static function error(array $raw): ?string
    {
        $value = $raw['error'] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new ValidationException('error must be a string');
        }

        return $value;
    }
}
