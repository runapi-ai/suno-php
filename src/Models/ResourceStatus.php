<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

/**
 * Availability of a RunAPI-owned resource.
 */
final class ResourceStatus
{
    public const AVAILABLE = 'available';
    public const FAILED = 'failed';

    /** @var list<string> */
    public const ALL = [self::AVAILABLE, self::FAILED];

    private function __construct()
    {
    }
}
