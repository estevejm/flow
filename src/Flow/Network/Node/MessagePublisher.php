<?php

namespace Flow\Network\Node;

use Flow\Network\Node;

class MessagePublisher extends Node
{
    /**
     * @var Message[]
     */
    private $messages = [];

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param Message $message
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
    }
}
