<?php

namespace Keboola\GmailExtractor;

use Symfony\Component\Filesystem\Filesystem;

class NestedPartsExtractorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    private $fs;

    private $path = '/tmp/extractor-nested-parts';

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
            new Query('subject:email with nested parts', [
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
"18cf37c1b055be37","18cf37b4f1328b1b"\n
CSV;
        $headersContents = <<<CSV
"messageId","name","value"
"18cf37c1b055be37","Date","Wed, 10 Jan 2024 14:07:25 +0100"
"18cf37c1b055be37","Subject","email with nested parts"
"18cf37c1b055be37","To","""Ondřej Jodas"" <ondrej.jodas@keboola.com>"\n
CSV;
        $partsContents = <<<CSV
"messageId","partId","mimeType","bodySize","bodyData"
"18cf37c1b055be37","0","text/plain","77"," Hello!

Please see the attached file for a list of customers to contact.
"
"18cf37c1b055be37","1","text/html","125","<div dir=""ltr"">


<h1>Hello!</h1>
<p>Please see the attached file for a list of customers to contact.</p>

<br></div>
"\n
CSV;
        $queriesContents = <<<CSV
"query","messageId"
"subject:email with nested parts","18cf37c1b055be37"\n
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
