<?php

namespace Keboola\GmailExtractor;

class MessagesResource
{
    /** @var \Google_Service_Gmail  */
    private $gmailService;

    public function __construct(\Google_Service_Gmail $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    /**
     * List messages by specified filter/params
     * @param $userId
     * @param $params
     * @return \Google_Service_Gmail_Message[]
     */
    public function listMessages($userId, $params)
    {
        $pageToken = null;
        $messages = [];

        do {
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }
            $messagesResponse = $this->gmailService->users_messages->listUsersMessages($userId, $params);
            if ($messagesResponse->getMessages()) {
                $messages = array_merge($messages, $messagesResponse->getMessages());
                $pageToken = $messagesResponse->getNextPageToken();
            }
        } while ($pageToken);

        return $messages;
    }

    /**
     * Gets single message
     * @param $id
     * @param $userId
     * @return \Google_Service_Gmail_Message
     */
    public function getMessage($id, $userId)
    {
        return $this->gmailService->users_messages->get($userId, $id, [
            'format' => 'full'
        ]);
    }
}
