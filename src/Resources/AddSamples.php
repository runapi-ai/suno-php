<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

/**
 * Adds a sample from an uploaded track to a new generation.
 *
 * @deprecated Use MusicFromSample instead, which names the sampled range of a RunAPI audio resource.
 */
readonly class AddSamples extends AudioResource
{
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'add_samples');
    }
}
