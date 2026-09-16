<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/**
 * Result of retrieving a persona resource: the persona, its availability, and the task that produced it.
 */
readonly class PersonaResourceResponse extends BaseModel
{
    /**
     * Create a persona resource response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public PersonaResource $persona,
        public string $status,
        public ResourceBilling $billing,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'persona' => $persona->toArray(),
            'status' => $status,
            'billing' => $billing->toArray(),
        ] : $raw);
    }

    /**
     * Hydrate a persona resource response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            persona: PersonaResource::fromArray(Payload::array($raw, 'persona')),
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
