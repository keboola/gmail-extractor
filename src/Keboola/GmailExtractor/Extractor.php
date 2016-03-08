<?php

namespace Keboola\GmailExtractor;

use Symfony\Component\Console\Output\ConsoleOutput;

class Extractor
{
    /** @var OutputFiles  */
    private $outputFiles;

    /** @var \Google_Service_Gmail  */
    private $gmailService;

    /**
     * Extractor constructor.
     * @param \Google_Service_Gmail $gmailService
     * @param OutputFiles $outputFiles
     */
    public function __construct(\Google_Service_Gmail $gmailService, OutputFiles $outputFiles)
    {
        $this->gmailService = $gmailService;
        $this->outputFiles = $outputFiles;
    }

    /**
     * Extracts messages by specified queries
     * @param $queries Query[]
     */
    public function extract($queries)
    {
        $output = new ConsoleOutput;
        $output->writeln('Queries: ' . count($queries));

        foreach ($queries as $query) {
            $q = $query->getQuery();
            $output->writeln('Processing query: ' . $q);

            $params = [
                'q' => $q,
            ];

            $pageToken = null;
            $count = 0;

            do {
                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $messagesResponse = $this->gmailService->users_messages->listUsersMessages('me', $params);
                $messages = $messagesResponse->getMessages();
                if ($messages) {
                    foreach ($messages as $message) {
                        /** @var \Google_Service_Gmail_Message $message */
                        $fullMessage = $this->gmailService->users_messages->get('me', $message->getId(), [
                            'format' => 'full'
                        ]);
                        $messageWriter = new MessageWriter($fullMessage, $this->outputFiles);
                        $messageWriter->setAllowedHeaders($query->getHeaders());
                        $messageWriter->save();
                    }
                    $pageToken = $messagesResponse->getNextPageToken();
                    $count += $messagesResponse->count();
                    $output->writeln('Processed results: ' . $count);
                }
            } while ($pageToken);

            $output->writeln('Done.');
        }
    }
}
