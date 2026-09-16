<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;

readonly class RemasterAudio extends AudioResource
{
    public const ACTION = 'suno/remaster-audio';

    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'remaster_audio', self::ACTION);
    }
}
