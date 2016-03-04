<?php

namespace Keboola\GmailExtractor;

class OutputFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testOutputFilesCreation()
    {
        $path = '/tmp';
        new OutputFiles($path);

        foreach (['messages', 'headers', 'parts'] as $name) {
            $this->assertFileExists($path . '/' . $name . '.csv');
            $this->assertFileExists($path . '/' . $name . '.csv.manifest');
        }
    }

    public function testCsvOutputFilesContents()
    {
        $path = '/tmp';
        $outputFiles = new OutputFiles($path);

        $messagesFileName = $outputFiles->getMessagesFile()->getFilename();
        $headersFileName = $outputFiles->getHeadersFile()->getFilename();
        $partsFileName = $outputFiles->getPartsFile()->getFilename();

        $expectedMessagesFileName = $path . '/expected-' . $messagesFileName;
        $expectedHeadersFileName = $path . '/expected-' . $headersFileName;
        $expectedPartsFileName = $path . '/expected-' . $partsFileName;

        file_put_contents($expectedMessagesFileName, '"id","threadId"' . "\n");
        file_put_contents($expectedHeadersFileName, '"messageId","name","value"' . "\n");
        file_put_contents($expectedPartsFileName, '"messageId","partId","mimeType","bodySize","bodyData"' . "\n");

        $this->assertFileEquals($expectedMessagesFileName, $path . '/' . $messagesFileName);
        $this->assertFileEquals($expectedHeadersFileName, $path . '/' . $headersFileName);
        $this->assertFileEquals($expectedPartsFileName, $path . '/' . $partsFileName);
    }

    public function testManifestOutputFilesContents()
    {
        $path = '/tmp';
        $outputFiles = new OutputFiles($path);

        $messagesFileName = $outputFiles->getMessagesFile()->getFilename() . '.manifest';
        $headersFileName = $outputFiles->getHeadersFile()->getFilename() . '.manifest';
        $partsFileName = $outputFiles->getPartsFile()->getFilename() . '.manifest';

        $expectedMessagesFileName = $path . '/expected-' . $messagesFileName . '.manifest';
        $expectedHeadersFileName = $path . '/expected-' . $headersFileName . '.manifest';
        $expectedPartsFileName = $path . '/expected-' . $partsFileName . '.manifest';

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

        file_put_contents($expectedMessagesFileName, $messagesManifestContents . "\n");
        file_put_contents($expectedHeadersFileName, $headersManifestContents . "\n");
        file_put_contents($expectedPartsFileName, $partsManifestContents . "\n");

        $this->assertFileEquals($expectedMessagesFileName, $path . '/' . $messagesFileName);
        $this->assertFileEquals($expectedHeadersFileName, $path . '/' . $headersFileName);
        $this->assertFileEquals($expectedPartsFileName, $path . '/' . $partsFileName);
    }
}
