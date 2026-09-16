<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

readonly class InspireMusic extends AudioResource
{
    public const ACTION = 'suno/inspire-music';

    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'inspire_music', self::ACTION);
    }
}
