<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();

$environments = [
    'ENV_GMAIL_EXTRACTOR_APP_KEY',
    'ENV_GMAIL_EXTRACTOR_APP_SECRET',
    'ENV_GMAIL_EXTRACTOR_ACCESS_TOKEN_JSON',
];

foreach ($environments as $environment) {
    if (empty(getenv($environment))) {
        throw new RuntimeException(sprintf('Missing environment "%s".', $environment));
    }
}
