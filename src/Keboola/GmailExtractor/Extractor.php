<?php

namespace Keboola\GmailExtractor;

class Extractor
{
    /** @var OutputFiles  */
    private $outputFiles;

    /** @var MessagesResource  */
    private $messages;

    /**
     * Extractor constructor.
     * @param MessagesResource $messages
     * @param OutputFiles $outputFiles
     */
    public function __construct(MessagesResource $messages, OutputFiles $outputFiles)
    {
        $this->messages = $messages;
        $this->outputFiles = $outputFiles;
    }

    /**
     * Extracts messages
     * @param $params
     */
    public function extract($params)
    {
        foreach ($this->messages->listMessages('me', $params) as $message) {
            $fullMessage = $this->messages->getMessage($message->getId(), 'me');
            $messageWriter = new MessageWriter($fullMessage, $this->outputFiles);
            $messageWriter->save();
        }
    }
}
