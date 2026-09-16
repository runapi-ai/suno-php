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
    /**
     * Create a resource using the shared RunAPI HTTP transport.
     *
     * @param string $endpoint Endpoint below /api/v1/suno, or the absolute catalog path of a resource the whole API shares.
     * @param string $action Full contract action key, for example suno/generate-lyrics.
     */
    public function __construct(HttpClient $http, string $endpoint, string $action)
    {
        parent::__construct(
            $http,
            str_starts_with($endpoint, '/') ? $endpoint : '/api/v1/suno/' . $endpoint,
            $action,
            LyricsTaskResponse::class,
            CompletedLyricsTaskResponse::class,
            [],
            self::resourceName($action),
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

    private static function resourceName(string $action): string
    {
        $separator = strrpos($action, '/');

        return $separator === false ? $action : substr($action, $separator + 1);
    }
}
