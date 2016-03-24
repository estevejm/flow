<?php

namespace FlowUI\FlowBundle\Model;

trait CanTriggerMessages
{

    /**
     * @var Event[]
     */
    private $messages = [];

    /**
     * @return Message[]
     */
    public function getMessage()
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
