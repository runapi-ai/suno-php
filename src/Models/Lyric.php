<?php

declare(strict_types=1);

namespace RunApi\Suno\Models;

use RunApi\Core\Models\BaseModel;
use RunApi\Core\Support\Payload;

/** A generated lyrics entry. */
readonly class Lyric extends BaseModel
{
    /** @param array<string, mixed> $raw */
    public function __construct(public string $text, public ?string $title = null, array $raw = [])
    {
        parent::__construct($raw === [] ? ['title' => $title, 'text' => $text] : $raw);
    }

    /** @param array<string, mixed> $raw */
    public static function fromArray(array $raw): self
    {
        return new self(
            text: Payload::string($raw, 'text'),
            title: Payload::optionalString($raw, 'title'),
            raw: $raw,
        );
    }
}
