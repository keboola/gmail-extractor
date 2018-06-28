<?php

namespace Keboola\GmailExtractor;

class MessageWriter
{
    /** @var \Google_Service_Gmail_Message  */
    private $message;

    /** @var OutputFiles  */
    private $outputFiles;

    /** @var array message part mime types to store  */
    private $allowedMimeTypes = [
        'text/plain',
        'text/html',
    ];

    /** @var Query */
    private $query;

    /** @var [] headers to save */
    private $allowedHeaders = [];

    public function __construct(\Google_Service_Gmail_Message $message, OutputFiles $outputFiles, Query $query)
    {
        $this->message = $message;
        $this->outputFiles = $outputFiles;
        $this->query = $query;
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
        $this->outputFiles->getQueriesFile()->writeRow([
            $this->query->getQuery(),
            $this->message->getId(),
        ]);

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
                    $messageHeader->getValue(),
                ]);
            }
        }

        $this->writePart($this->message->getPayload());
    }

    /**
     * @param \Google_Service_Gmail_MessagePart $messagePart
     * @throws \Keboola\Csv\Exception
     */
    private function writePart(\Google_Service_Gmail_MessagePart $messagePart)
    {
        if ($this->isMimeTypeAllowed($messagePart) && $messagePart->getBody()->getData() !== null) {
            $this->outputFiles->getPartsFile()->writeRow([
                $this->message->getId(),
                $messagePart->getPartId(),
                $messagePart->getMimeType(),
                $messagePart->getBody()['size'],
                Base64Url::decode($messagePart->getBody()['data']),
            ]);
        }

        $nestedParts = $messagePart->getParts();

        if (!empty($nestedParts)) {
            foreach ($nestedParts as $nestedPart) {
                $this->writePart($nestedPart);
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
