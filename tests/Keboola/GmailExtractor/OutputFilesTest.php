<?php

namespace Keboola\GmailExtractor;

use Symfony\Component\Filesystem\Filesystem;

class OutputFilesTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    private $fs;

    private $path = '/tmp/csv-files';

    protected function setUp()
    {
        $this->fs = new Filesystem;
        $this->fs->remove($this->path);
        $this->fs->mkdir($this->path);
    }

    protected function tearDown()
    {
        $this->fs->remove($this->path);
    }

    public function testOutputFilesCreation()
    {
        new OutputFiles($this->path);

        foreach (['messages', 'headers', 'parts'] as $name) {
            $this->assertFileExists($this->path . '/' . $name . '.csv');
            $this->assertFileExists($this->path . '/' . $name . '.csv.manifest');
        }
    }

    public function testCsvOutputFilesContents()
    {
        $path = $this->path;
        $outputFiles = new OutputFiles($path);

        $messagesFileName = $outputFiles->getMessagesFile()->getFilename();
        $headersFileName = $outputFiles->getHeadersFile()->getFilename();
        $partsFileName = $outputFiles->getPartsFile()->getFilename();
        $queriesFileName = $outputFiles->getQueriesFile()->getFilename();

        $expectedMessagesFileName = $path . '/expected-' . $messagesFileName;
        $expectedHeadersFileName = $path . '/expected-' . $headersFileName;
        $expectedPartsFileName = $path . '/expected-' . $partsFileName;
        $expectedQueriesFileName = $path . '/expected-' . $queriesFileName;

        file_put_contents($expectedMessagesFileName, '"id","threadId"' . "\n");
        file_put_contents($expectedHeadersFileName, '"messageId","name","value"' . "\n");
        file_put_contents($expectedPartsFileName, '"messageId","partId","mimeType","bodySize","bodyData"' . "\n");
        file_put_contents($expectedQueriesFileName, '"query","messageId"' . "\n");

        $this->assertFileEquals($expectedMessagesFileName, $path . '/' . $messagesFileName);
        $this->assertFileEquals($expectedHeadersFileName, $path . '/' . $headersFileName);
        $this->assertFileEquals($expectedPartsFileName, $path . '/' . $partsFileName);
        $this->assertFileEquals($expectedQueriesFileName, $path . '/' . $queriesFileName);
    }

    public function testManifestOutputFilesContents()
    {
        $path = $this->path;
        $outputFiles = new OutputFiles($path);

        $messagesFileName = $outputFiles->getMessagesFile()->getFilename() . '.manifest';
        $headersFileName = $outputFiles->getHeadersFile()->getFilename() . '.manifest';
        $partsFileName = $outputFiles->getPartsFile()->getFilename() . '.manifest';
        $queriesFileName = $outputFiles->getQueriesFile()->getFilename() . '.manifest';

        $expectedMessagesFileName = $path . '/expected-' . $messagesFileName . '.manifest';
        $expectedHeadersFileName = $path . '/expected-' . $headersFileName . '.manifest';
        $expectedPartsFileName = $path . '/expected-' . $partsFileName . '.manifest';
        $expectedQueriesFileName = $path . '/expected-' . $queriesFileName . '.manifest';

        $messagesManifestContents = <<<YAML
incremental: true
primary_key:
    - id
destination: out.c-ex-gmail.messages
YAML;
        $headersManifestContents = <<<YAML
incremental: true
primary_key:
    - messageId
    - name
    - value
destination: out.c-ex-gmail.headers
YAML;
        $partsManifestContents = <<<YAML
incremental: true
primary_key:
    - messageId
    - partId
destination: out.c-ex-gmail.parts
YAML;
        $queriesManifestContents = <<<YAML
incremental: true
primary_key:
    - query
    - messageId
destination: out.c-ex-gmail.queries
YAML;

        file_put_contents($expectedMessagesFileName, $messagesManifestContents . "\n");
        file_put_contents($expectedHeadersFileName, $headersManifestContents . "\n");
        file_put_contents($expectedPartsFileName, $partsManifestContents . "\n");
        file_put_contents($expectedQueriesFileName, $queriesManifestContents . "\n");

        $this->assertFileEquals($expectedMessagesFileName, $path . '/' . $messagesFileName);
        $this->assertFileEquals($expectedHeadersFileName, $path . '/' . $headersFileName);
        $this->assertFileEquals($expectedPartsFileName, $path . '/' . $partsFileName);
        $this->assertFileEquals($expectedQueriesFileName, $path . '/' . $queriesFileName);
    }
}
