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

    /** @var [] headers to save */
    private $allowedHeaders = [];

    public function __construct(\Google_Service_Gmail_Message $message, OutputFiles $outputFiles)
    {
        $this->message = $message;
        $this->outputFiles = $outputFiles;
    }

    /**
     * Sets allowed headers
     * @param $headers []
     */
    public function setAllowedHeaders($headers)
    {
        $this->allowedHeaders = $headers;
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
            if ($this->isHeaderAllowed($messageHeader)) {
                $this->outputFiles->getHeadersFile()->writeRow([
                    $this->message->getId(),
                    $messageHeader->getName(),
                    $messageHeader->getValue()
                ]);
            }
        }

        $messageParts = $this->message->getPayload()['parts'];

        foreach ($messageParts as $messagePart) {
            /** @var $messagePart \Google_Service_Gmail_MessagePart */
            if ($this->isMimeTypeAllowed($messagePart)) {
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

    /**
     * Checks if header is marked for saving
     * @param \Google_Service_Gmail_MessagePartHeader $header
     * @return bool
     */
    private function isHeaderAllowed(\Google_Service_Gmail_MessagePartHeader $header)
    {
        return empty($this->allowedHeaders) || in_array($header->getName(), $this->allowedHeaders);
    }

    /**
     * Checks if message part's mime type is marked for saving
     * @param \Google_Service_Gmail_MessagePart $part
     * @return bool
     */
    private function isMimeTypeAllowed(\Google_Service_Gmail_MessagePart $part)
    {
        return empty($this->allowedMimeTypes) || in_array($part->getMimeType(), $this->allowedMimeTypes);
    }
}
