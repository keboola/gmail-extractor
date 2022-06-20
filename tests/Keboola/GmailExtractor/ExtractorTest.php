<?php

namespace Keboola\GmailExtractor;

use Symfony\Component\Filesystem\Filesystem;

class ExtractorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    private $fs;

    private $path = '/tmp/extractor';

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

    public function testExtract()
    {
        $path = $this->path;
        $outputFiles = new OutputFiles($path);

        $client = new \Google_Client;
        $client->setApplicationName('Keboola Gmail Extractor (dev)');
        $client->setScopes(\Google_Service_Gmail::GMAIL_READONLY);
        $client->setClientId(getenv('ENV_GMAIL_EXTRACTOR_APP_KEY'));
        $client->setClientSecret(getenv('ENV_GMAIL_EXTRACTOR_APP_SECRET'));
        $client->setAccessType('offline');
        $client->setAccessToken(getenv('ENV_GMAIL_EXTRACTOR_ACCESS_TOKEN_JSON'));
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
        }

        $extractor = new Extractor(new \Google_Service_Gmail($client), $outputFiles);
        $extractor->extract([
            new Query('subject:keboola-gmail-extractor-test-email', [
                'To',
                'Subject',
                'Date',
            ])
        ]);

        $messagesFileName = $outputFiles->getMessagesFile()->getFilename();
        $headersFileName = $outputFiles->getHeadersFile()->getFilename();
        $partsFileName = $outputFiles->getPartsFile()->getFilename();
        $queriesFileName = $outputFiles->getQueriesFile()->getFilename();

        $messagesContents = <<<CSV
"id","threadId"
"15341d3b36d2fccc","15341d3b36d2fccc"\n
CSV;
        $headersContents = <<<CSV
"messageId","name","value"
"15341d3b36d2fccc","Date","Fri, 4 Mar 2016 14:31:45 +0100"
"15341d3b36d2fccc","Subject","keboola-gmail-extractor-test-email"
"15341d3b36d2fccc","To","vlado@keboola.com"\n
CSV;
        $partsContents = <<<CSV
"messageId","partId","mimeType","bodySize","bodyData"
"15341d3b36d2fccc","0","text/plain","25","https://www.keboola.com
"
"15341d3b36d2fccc","1","text/html","84","<div dir=""ltr""><a href=""https://www.keboola.com"">https://www.keboola.com</a></div>
"\n
CSV;
        $queriesContents = <<<CSV
"query","messageId"
"subject:keboola-gmail-extractor-test-email","15341d3b36d2fccc"\n
CSV;

        $this->assertFileExists($path . '/' . $messagesFileName);
        $this->assertFileExists($path . '/' . $headersFileName);
        $this->assertFileExists($path . '/' . $partsFileName);
        $this->assertFileExists($path . '/' . $queriesFileName);

        $this->assertEquals($messagesContents, $this->normalizeNewlines(file_get_contents($path . '/' . $messagesFileName)));
        $this->assertEquals($headersContents, $this->normalizeNewlines(file_get_contents($path . '/' . $headersFileName)));
        $this->assertEquals($partsContents, $this->normalizeNewlines(file_get_contents($path . '/' . $partsFileName)));
        $this->assertEquals($queriesContents, $this->normalizeNewlines(file_get_contents($path . '/' . $queriesFileName)));
    }

    /**
     * Replaces all non-standard newlines with "\n"
     * @param $string
     * @return string
     */
    private function normalizeNewlines($string)
    {
        return str_replace(["\r\n", "\r"], "\n", $string);
    }
}
