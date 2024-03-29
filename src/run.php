<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Keboola\GmailExtractor\OutputFiles;
use Keboola\GmailExtractor\Query;
use Keboola\GmailExtractor\StateFiles;

$arguments = getopt("d::", ["data:"]);
if (!isset($arguments['data'])) {
    echo 'Data folder not set.' . "\n";
    exit(1);
}

try {
    $configContent = (string) file_get_contents($arguments['data'] . "/config.json");
    $config = json_decode($configContent, true);

    if (!isset($config['authorization']['oauth_api']['credentials']['appKey'])
        || !isset($config['authorization']['oauth_api']['credentials']['#appSecret'])) {
        echo 'App configuration is missing parameter #client_id or #client_secret, contact support please.' . "\n";
        exit(1);
    }
    if (!isset($config['authorization']['oauth_api']['credentials']['#data'])) {
        echo 'App configuration is missing #data, contact support please.'. "\n";
        exit(1);
    }
    if (!isset($config['parameters']['queries'])) {
        echo 'Please specify queries.' . "\n";
        exit(1);
    }
    if (!is_array($config['parameters']['queries'])) {
        echo 'Queries must be specified as array.' . "\n";
        exit(1);
    }

    $stateFiles = new StateFiles($arguments['data']);
    if (($state = $stateFiles->getStateIn()) === null) {
        $state = [
            'query-dates' => [],
        ];
    }

    $queries = [];
    foreach ($config['parameters']['queries'] as $item) {
        if (!isset($item['query'])) {
            echo 'Parameter query must be specified.' . "\n";
            exit(1);
        }
        $headers = [];
        if (isset($item['headers'])) {
            if (!is_array($item['headers'])) {
                echo 'Parameter headers must be specified as array.' . "\n";
                exit(1);
            }
            $headers = $item['headers'];
        }
        $queryString = trim($item['query']);
        if (isset($state['query-dates'][$queryString])) {
            $query = new Query($queryString, $headers, new DateTime($state['query-dates'][$queryString] . ' -1 day'));
        } else {
            $query = new Query($queryString, $headers);
        }
        $state['query-dates'][$queryString] = (new DateTime)->format('Y-m-d H:i:s');
        $queries[] = $query;
    }

    $outputPath = $arguments['data'] . '/out/tables';
    if (!file_exists($outputPath)) {
        mkdir($outputPath, 0755, true);
    }

    $stateFiles->setStateOut($state);
    $stateFiles->saveStateOut();

    $client = new Google_Client;
    $client->setApplicationName('Keboola Gmail Extractor');
    $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
    $client->setClientId($config['authorization']['oauth_api']['credentials']['appKey']);
    $client->setClientSecret($config['authorization']['oauth_api']['credentials']['#appSecret']);
    $client->setAccessType('offline');
    $client->setAccessToken($config['authorization']['oauth_api']['credentials']['#data']);
    if ($client->isAccessTokenExpired()) {
        $client->refreshToken($client->getRefreshToken());
    }

    $container = new ContainerBuilder;
    $container
        ->register('extractor', 'Keboola\GmailExtractor\Extractor')
        ->addArgument(new Google_Service_Gmail($client))
        ->addArgument(new OutputFiles($outputPath));

    /** @var \Keboola\GmailExtractor\Extractor $extractor */
    $extractor = $container->get('extractor');
    $extractor->extract($queries);
    exit(0);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
