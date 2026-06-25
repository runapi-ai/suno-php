<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Contract\ContractValidator;
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
     * @param list<string> $requiredFields
     */
    public function __construct(
        protected HttpClient $http,
        private string $endpoint,
        private string $action,
        private string $responseClass,
        private array $requiredFields = [],
        protected ContractValidator $validator = new ContractValidator(),
    ) {
    }

    /**
     * Submit the sync resource request and return the result.
     *
     * @param array<string, mixed> $params
     */
    public function run(array $params, ?RequestOptions $options = null): BaseModel
    {
        $params = $this->compact($params);
        $model = $this->model($params);
        $this->validator->validate($this->action, $model, $params);
        $this->validateRequiredFields($params);

        $factory = [$this->responseClass, 'fromArray'];
        if (!is_callable($factory)) {
            throw new ValidationException($this->responseClass . ' must define fromArray');
        }

        $response = $factory($this->http->request('post', $this->endpoint, [
            'body' => $params,
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

    /**
     * @param array<string, mixed> $params
     */
    protected function model(array $params): string
    {
        $model = $params['model'] ?? null;
        if ($model === null || $model === '') {
            return '_';
        }

        if (!is_string($model)) {
            throw new ValidationException('model must be a string');
        }

        return $model;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function validateRequiredFields(array $params): void
    {
        foreach ($this->requiredFields as $field) {
            if (!array_key_exists($field, $params) || $params[$field] === null || $params[$field] === '') {
                throw new ValidationException($field . ' is required');
            }
        }
    }
}
