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
"162973353bade11a","162973353bade11a"\n
CSV;
        $headersContents = <<<CSV
"messageId","name","value"
"162973353bade11a","To","vlado@keboola.com"
"162973353bade11a","Subject","email with nested parts"
"162973353bade11a","Date","Thu, 5 Apr 2018 21:05:46 +0200"\n
CSV;
        $partsContents = <<<CSV
"messageId","partId","mimeType","bodySize","bodyData"
"162973353bade11a","0.0","text/plain","65","Please see the attached file for a list of customers to contact.
"
"162973353bade11a","0.1","text/html","132","<html>
<head></head>
<body>
<h1>Hello!</h1>
<p>Please see the attached file for a list of customers to contact.</p>
</body>
</html>
"\n
CSV;
        $queriesContents = <<<CSV
"query","messageId"
"subject:email with nested parts","162973353bade11a"\n
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
