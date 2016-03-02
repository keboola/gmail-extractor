<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Yaml;
use Keboola\GmailExtractor\OutputFiles;

$arguments = getopt("d::", ["data:"]);
if (!isset($arguments['data'])) {
    echo 'Data folder not set.' . "\n";
    exit(1);
}

try {
    $config = Yaml::parse(file_get_contents($arguments['data'] . "/config.yml"));

    if (!isset($config['image_parameters']['#client_id']) || !isset($config['image_parameters']['#client_secret'])) {
        echo 'App configuration is missing parameter #client_id or #client_secret, contact support please.' . "\n";
        exit(1);
    }
    if (!isset($config['authorization']['oauth_api']['credentials']['#data'])) {
        echo 'App configuration is missing #data, contact support please.'. "\n";
        exit(1);
    }
    if (!isset($config['parameters']['q'])) {
        echo 'Please provide query parameter (q).' . "\n";
        exit(1);
    }

    $outputPath = $arguments['data'] . '/out/tables';
    if (!file_exists($outputPath)) {
        mkdir($outputPath, 0755, true);
    }

    $params = [
        'q' => $config['parameters']['q'],
    ];

    $client = new Google_Client;
    $client->setApplicationName('Keboola Gmail Extractor');
    $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
    $client->setClientId($config['image_parameters']['#client_id']);
    $client->setClientSecret($config['image_parameters']['#client_secret']);
    $client->setAccessType('offline');
    $client->setAccessToken($config['authorization']['oauth_api']['credentials']['#data']);
    if ($client->isAccessTokenExpired()) {
        $client->refreshToken($client->getRefreshToken());
    }

    $container = new ContainerBuilder;
    $container
        ->register('messages', 'Keboola\GmailExtractor\MessagesResource')
        ->addArgument(new Google_Service_Gmail($client));
    $container
        ->register('extractor', 'Keboola\GmailExtractor\Extractor')
        ->addArgument(new Reference('messages'))
        ->addArgument(new OutputFiles($outputPath));

    $extractor = $container->get('extractor');
    $extractor->extract($params);
    exit(0);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
