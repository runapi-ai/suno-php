<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\RequestOptions;
use RunApi\Suno\Models\CheckVoiceResponse;

/**
 * Checks whether a custom voice from generateVoice is ready for use. Synchronous (run() only).
 */
readonly class CheckVoice extends SyncResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http);
    }

    /**
     * Create a resource using the shared RunAPI HTTP transport.
     */
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, '/api/v1/suno/check_voice', 'suno/check-voice', CheckVoiceResponse::class, ['task_id']);
    }

    /**
     * Checks whether a custom voice is ready for use and returns the result.
     *
     * @param array{task_id: string} $params
     */
    public function run(array $params, ?RequestOptions $options = null): CheckVoiceResponse
    {
        $response = parent::run($params, $options);

        /** @var CheckVoiceResponse $response */
        return $response;
    }
}
