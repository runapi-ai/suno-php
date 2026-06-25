# Suno PHP SDK for RunAPI

[![Packagist](https://img.shields.io/packagist/v/runapi-ai/suno)](https://packagist.org/packages/runapi-ai/suno)
[![License](https://img.shields.io/github/license/runapi-ai/suno-php)](https://github.com/runapi-ai/suno-php/blob/main/LICENSE)

The Suno PHP SDK is the Composer package for Suno on RunAPI. Use it when your PHP application needs associative-array request bodies, task status lookup, polling helpers, file helpers, and consistent RunAPI errors.

## Install

```bash
composer require runapi-ai/suno
```

## Quick start

```php
<?php

require __DIR__ . "/vendor/autoload.php";

use RunApi\Suno\SunoClient;

$client = new SunoClient(); // reads RUNAPI_API_KEY

$task = $client->textToMusic->create([
    'model' => 'suno-v5.5',
    'prompt' => 'A chill lo-fi beat with soft vocals',
    'vocal_mode' => 'auto_lyrics',
]);

$status = $client->textToMusic->get($task->id);

$result = $client->textToMusic->run([
    'model' => 'suno-v5.5',
    'prompt' => 'A cinematic synthwave track with bright drums',
    'vocal_mode' => 'auto_lyrics',
]);

print_r($result->toArray());
```

Use `create()` to submit a task and return quickly, `get()` to fetch the latest task state, and `run()` when a script should create and poll until completion. In web request handlers, prefer `create()` plus webhook or later `get()` polling so a worker is not held open.

Returned file URLs are temporary. Download and store generated files in your own durable storage within the retention window.

All SDK exceptions inherit from `RunApi\Core\Errors\RunApiException`, including validation, authentication, rate limit, task failure, and task timeout errors.

## Links

- Model page: https://runapi.ai/models/suno
- SDK docs: https://runapi.ai/docs#sdk-suno
- Product docs: https://runapi.ai/docs#suno
- Pricing and rate limits: https://runapi.ai/models/suno/v4
- Full catalog: https://runapi.ai/models
- GitHub repository: https://github.com/runapi-ai/suno-php
- Multi-language SDK repository: https://github.com/runapi-ai/suno-sdk

## License

Licensed under the Apache License, Version 2.0.
