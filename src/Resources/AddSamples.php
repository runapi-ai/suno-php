<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;

/**
 * Adds a sample from an uploaded track to a new generation.
 *
 * @deprecated Use MusicFromSample instead, which names the sampled range of a RunAPI audio resource.
 */
readonly class AddSamples extends AudioResource
{
    public const ACTION = 'suno/add-samples';

    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'add_samples', self::ACTION);
    }

    protected function validate(array $params, string $model): void
    {
        parent::validate($params, $model);
        $start = $params['start_seconds'] ?? null;
        $end = $params['end_seconds'] ?? null;
        if ((is_int($start) || is_float($start)) && (is_int($end) || is_float($end)) && $end <= $start) {
            throw new ValidationException('end_seconds must be greater than start_seconds');
        }
    }
}
