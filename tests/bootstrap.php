<?php

require __DIR__ . '/../vendor/autoload.php';

$appKey = getenv('ENV_GMAIL_EXTRACTOR_APP_KEY');
$appSecret = getenv('ENV_GMAIL_EXTRACTOR_APP_SECRET');
$accessTokenJson = getenv('ENV_GMAIL_EXTRACTOR_ACCESS_TOKEN_JSON');

foreach ([$appKey, $appSecret, $accessTokenJson] as $var) {
    if ($var === false) {
        echo 'Set all required environment variables' . "\n";
        exit(1);
    }
}

define('GMAIL_EXTRACTOR_APP_KEY', $appKey);
define('GMAIL_EXTRACTOR_APP_SECRET', $appSecret);
define('GMAIL_EXTRACTOR_ACCESS_TOKEN_JSON', $accessTokenJson);
