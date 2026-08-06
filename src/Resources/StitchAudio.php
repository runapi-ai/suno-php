<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

readonly class StitchAudio extends AudioResource
{
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'stitch_audio', 'stitch-audio');
    }
}
