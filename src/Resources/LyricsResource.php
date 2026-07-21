<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\Suno\Models\CompletedLyricsTaskResponse;
use RunApi\Suno\Models\LyricsTaskResponse;

/** Shared async resource for endpoints returning lyrics entries. */
readonly class LyricsResource extends TypedConfiguredResource
{
    public function __construct(HttpClient $http, string $endpointName, string $actionName)
    {
        parent::__construct(
            $http,
            '/api/v1/suno/' . $endpointName,
            'suno/' . $actionName,
            LyricsTaskResponse::class,
            CompletedLyricsTaskResponse::class,
            [],
            $actionName,
            LyricsTaskResponse::class,
            CompletedLyricsTaskResponse::class,
        );
    }

    /** @param array<string, mixed> $params */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    public function get(string $id, ?RequestOptions $options = null): LyricsTaskResponse
    {
        $response = parent::get($id, $options);
        /** @var LyricsTaskResponse $response */
        return $response;
    }

    /** @param array<string, mixed> $params */
    public function run(array $params, ?RequestOptions $options = null): CompletedLyricsTaskResponse
    {
        $response = parent::run($params, $options);
        /** @var CompletedLyricsTaskResponse $response */
        return $response;
    }
}
