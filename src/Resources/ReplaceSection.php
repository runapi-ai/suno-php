<?php

declare(strict_types=1);

namespace RunApi\Suno\Resources;

use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Http\HttpClient;

/**
 * Re-generates a time range within an existing track with new lyrics and style.
 */
readonly class ReplaceSection extends AudioResource
{
    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self($http, 'replace_section', 'replace-section');
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function validate(array $params, string $model): void
    {
        parent::validate($params, $model);
        $this->validateSource($params);
        $this->validateTimeRange($params);
    }

    /**
     * @param array<string, mixed> $params
     */
    private function validateSource(array $params): void
    {
        $hasExistingSource = $this->hasAny($params, ['task_id', 'audio_id']);
        $hasUploadedSource = $this->hasAny($params, ['upload_url', 'model']);

        if ($hasExistingSource && $hasUploadedSource) {
            throw new ValidationException('task_id/audio_id cannot be combined with upload_url/model');
        }

        if ($hasExistingSource) {
            $this->requireField($params, 'task_id');
            $this->requireField($params, 'audio_id');
            return;
        }

        if ($hasUploadedSource) {
            $this->requireField($params, 'upload_url');
            $this->requireField($params, 'model');
            return;
        }

        throw new ValidationException('task_id and audio_id, or upload_url and model are required');
    }

    /**
     * @param array<string, mixed> $params
     */
    private function validateTimeRange(array $params): void
    {
        $startTime = $this->numberValue($params, 'infill_start_time');
        $endTime = $this->numberValue($params, 'infill_end_time');

        if ($endTime <= $startTime) {
            throw new ValidationException('infill_end_time must be greater than infill_start_time');
        }

        $duration = $endTime - $startTime;
        if ($duration < 6.0 || $duration > 60.0) {
            throw new ValidationException('replacement duration must be between 6 and 60 seconds');
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    private function numberValue(array $params, string $field): float
    {
        $value = $params[$field] ?? null;
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        throw new ValidationException($field . ' must be a number');
    }

    /**
     * @param array<string, mixed> $params
     * @param list<string> $fields
     */
    private function hasAny(array $params, array $fields): bool
    {
        foreach ($fields as $field) {
            if ($this->hasValue($params, $field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function hasValue(array $params, string $field): bool
    {
        if (!array_key_exists($field, $params)) {
            return false;
        }

        $value = $params[$field];
        return $value !== null && $value !== '' && $value !== [];
    }
}
