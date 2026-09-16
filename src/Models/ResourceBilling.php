<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\BaseModel;

/** Billing envelope for a RunAPI-owned resource. Resource provenance is opaque. */
readonly class ResourceBilling extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public function __construct(array $raw = [])
    {
        parent::__construct($raw);
    }

    /**
     * Hydrate resource billing from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self($raw);
    }
}
