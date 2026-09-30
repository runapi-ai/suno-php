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
     * @param string $endpoint Endpoint below /api/v1/suno, or the absolute catalog path of a resource the whole API shares.
     * @param class-string<AudioTaskResponse> $responseClass
     * @param class-string<CompletedAudioTaskResponse> $completedResponseClass
     */
    public function __construct(
        HttpClient $http,
        string $endpoint,
        string $responseClass = AudioTaskResponse::class,
        string $completedResponseClass = CompletedAudioTaskResponse::class,
    ) {
        parent::__construct(
            $http,
            self::resolveEndpoint($endpoint),
            $responseClass,
            $completedResponseClass,
            self::resourceName($endpoint),
            $responseClass,
            $completedResponseClass,
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

    private static function resolveEndpoint(string $endpoint): string
    {
        return str_starts_with($endpoint, '/') ? $endpoint : '/api/v1/suno/' . $endpoint;
    }
    private static function resourceName(string $endpoint): string
    {
        $endpoint = trim($endpoint, '/');
        $separator = strrpos($endpoint, '/');

        return $separator === false ? $endpoint : substr($endpoint, $separator + 1);
    }

}
