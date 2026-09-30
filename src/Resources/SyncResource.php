<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\BaseModel;
use RunApi\Core\RequestOptions;

/**
 * Sync resource operations for Suno.
 */
abstract readonly class SyncResource
{
    /**
     * Create a resource using the shared RunAPI HTTP transport.
     *
     * @param class-string<BaseModel> $responseClass
     */
    public function __construct(
        protected HttpClient $http,
        private string $endpoint,
        private string $responseClass,
    ) {
    }

    /**
     * Submit the sync resource request and return the result.
     *
     * @param array<string, mixed> $params
     */
    public function run(array $params, ?RequestOptions $options = null): BaseModel
    {
        $factory = [$this->responseClass, 'fromArray'];
        if (!is_callable($factory)) {
            throw new ValidationException($this->responseClass . ' must define fromArray');
        }

        $response = $factory($this->http->request('post', $this->endpoint, [
            'body' => $this->compact($params),
            'options' => $options,
        ]));
        if (!$response instanceof BaseModel) {
            throw new ValidationException($this->responseClass . ' must return a BaseModel');
        }

        return $response;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    protected function compact(array $params): array
    {
        $result = [];
        foreach ($params as $key => $value) {
            if ($value === null || $value === '' || (is_array($value) && $value === [])) {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
