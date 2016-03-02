<?php

namespace Keboola\GmailExtractor;

use Keboola\Csv\CsvFile;
use Symfony\Component\Yaml\Yaml;

class OutputFiles
{
    /** @var string output path */
    private $path;

    /** @var [] files/tables spec */
    protected $definitions = [
        'messages' => [
            'file' => 'messages.csv',
            'columns' => [
                'id',
                'threadId',
            ],
            'primary' => ['id'],
        ],
        'headers' => [
            'file' => 'headers.csv',
            'columns' => [
                'messageId',
                'name',
                'value',
            ],
            'primary' => ['messageId', 'name', 'value'],
        ],
        'parts' => [
            'file' => 'parts.csv',
            'columns' => [
                'messageId',
                'partId',
                'mimeType',
                'bodySize',
                'bodyData',
            ],
            'primary' => ['messageId', 'partId'],
        ]
    ];

    /**
     * Prepares output files (both .csv and .manifest) with initial content.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;

        $this->messagesFile = new CsvFile($this->path . '/' . $this->definitions['messages']['file']);
        $this->messagesFile->writeRow($this->definitions['messages']['columns']);

        $this->headersFile = new CsvFile($this->path . '/' . $this->definitions['headers']['file']);
        $this->headersFile->writeRow($this->definitions['headers']['columns']);

        $this->partsFile = new CsvFile($this->path . '/' . $this->definitions['parts']['file']);
        $this->partsFile->writeRow($this->definitions['parts']['columns']);

        $this->createManifestFiles();
    }

    /**
     * Gets file for messages
     * @return CsvFile
     */
    public function getMessagesFile()
    {
        return $this->messagesFile;
    }

    /**
     * Gets file for message headers
     * @return CsvFile
     */
    public function getHeadersFile()
    {
        return $this->headersFile;
    }

    /**
     * Gets file for message part
     * @return CsvFile
     */
    public function getPartsFile()
    {
        return $this->partsFile;
    }

    /**
     * Creates manifest files
     */
    private function createManifestFiles()
    {
        foreach ($this->definitions as $table => $definition) {
            $manifestFile = $this->path . '/' . $definition['file'] . '.manifest';
            if (!file_exists($manifestFile)) {
                file_put_contents($manifestFile, Yaml::dump([
                    'incremental' => true,
                    'primary_key' => $definition['primary'],
                    'destination' => $table,
                ]));
            }
        }
    }
}
