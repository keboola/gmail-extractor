<?php

namespace Keboola\GmailExtractor;

class Extractor
{
    /** @var OutputFiles  */
    private $storage;

    /** @var MessagesResource  */
    private $messages;

    /**
     * Extractor constructor.
     * @param MessagesResource $messages
     * @param OutputFiles $storage
     */
    public function __construct(MessagesResource $messages, OutputFiles $storage)
    {
        $this->messages = $messages;
        $this->storage = $storage;
    }

    /**
     * Extracts messages
     * @param $params
     */
    public function extract($params)
    {
        foreach ($this->messages->listMessages('me', $params) as $message) {
            $fullMessage = $this->messages->getMessage($message->getId(), 'me');
            $messageWriter = new MessageWriter($fullMessage, $this->storage);
            $messageWriter->save();
        }
    }
}
