<?php

namespace Keboola\GmailExtractor;

class MessageWriter
{
    /** @var \Google_Service_Gmail_Message  */
    private $message;

    /** @var OutputFiles  */
    private $outputFiles;

    /** @var [] message part mime types to store  */
    private $allowedMimeTypes = [
        'text/plain',
        'text/html',
    ];

    public function __construct(\Google_Service_Gmail_Message $message, OutputFiles $outputFiles)
    {
        $this->message = $message;
        $this->outputFiles = $outputFiles;
    }

    /**
     * Saves message to output files
     * @throws \Keboola\Csv\Exception
     */
    public function save()
    {
        $this->outputFiles->getMessagesFile()->writeRow([
            $this->message->getId(),
            $this->message->getThreadId(),
        ]);

        $messageHeaders = $this->message->getPayload()['headers'];

        foreach ($messageHeaders as $messageHeader) {
            /** @var $messageHeader \Google_Service_Gmail_MessagePartHeader */
            $this->outputFiles->getHeadersFile()->writeRow([
                $this->message->getId(),
                $messageHeader->getName(),
                $messageHeader->getValue()
            ]);
        }

        $messageParts = $this->message->getPayload()['parts'];

        foreach ($messageParts as $messagePart) {
            /** @var $messagePart \Google_Service_Gmail_MessagePart */
            if (in_array($messagePart->getMimeType(), $this->allowedMimeTypes)) {
                $this->outputFiles->getPartsFile()->writeRow([
                    $this->message->getId(),
                    $messagePart->getPartId(),
                    $messagePart->getMimeType(),
                    $messagePart->getBody()['size'],
                    Base64Url::decode($messagePart->getBody()['data']),
                ]);
            }
        }
    }
}
