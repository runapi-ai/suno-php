<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/**
 * Result of creating a persona.
 *
 * A request the service completed carries persona. A request it accepted for
 * local execution carries the task acceptance (id and status) instead, and the
 * result is read from the task status endpoint.
 */
readonly class PersonaCreationResponse extends BaseModel
{
    /**
     * Create a persona creation response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public ?string $id = null,
        public ?string $status = null,
        public ?PersonaResource $persona = null,
        public ?string $error = null,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'id' => $id,
            'status' => $status,
            'persona' => $persona?->toArray(),
            'error' => $error] : $raw);
    }

    /**
     * Hydrate a persona creation response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::optionalString($raw, 'id'),
            status: Payload::optionalString($raw, 'status'),
            persona: self::persona($raw),
            error: Payload::optionalString($raw, 'error'),
            raw: $raw,
        );
    }

    /** @param array<string, mixed> $raw */
    private static function persona(array $raw): ?PersonaResource
    {
        $value = $raw['persona'] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            throw new ValidationException('persona must be an object');
        }

        /** @var array<string, mixed> $value */
        return PersonaResource::fromArray($value);
    }
}
