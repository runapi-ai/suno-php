<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\Suno\Models\AudioTaskResponse;
use RunApi\Suno\Models\CompletedAudioTaskResponse;

/**
 * Audio resource operations for Suno.
 */
readonly class AudioResource extends TypedConfiguredResource
{
    /**
     * Create a resource using the shared RunAPI HTTP transport.
     *
     * @param list<string> $models
     */
    public function __construct(HttpClient $http, string $endpointName, string $actionName, array $models = [])
    {
        parent::__construct(
            $http,
            '/api/v1/suno/' . $endpointName,
            'suno/' . $actionName,
            AudioTaskResponse::class,
            CompletedAudioTaskResponse::class,
            $models,
            $actionName,
            AudioTaskResponse::class,
            CompletedAudioTaskResponse::class,
        );
    }

    /**
     * Create an audio resource task and return immediately with a task id.
     *
     * @param array<string, mixed> $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Fetch the current status of an audio resource task.
     */
    public function get(string $id, ?RequestOptions $options = null): AudioTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var AudioTaskResponse $response */
        return $response;
    }

    /**
     * Submit an audio resource task and poll until it completes.
     *
     * @param array<string, mixed> $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedAudioTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedAudioTaskResponse $response */
        return $response;
    }
}
