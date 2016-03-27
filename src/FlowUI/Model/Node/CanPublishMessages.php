<?php

namespace FlowUI\Model\Node;

trait CanPublishMessages
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
