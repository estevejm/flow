<?php

namespace FlowUI\FlowBundle\Model;

class Subscriber extends Node
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var Event[]
     */
    private $messages;

    /**
     * @param string $id
     * @param string $className
     * @param Event $event
     */
    public function __construct($id, $className, Event $event)
    {
        parent::__construct($id, 'subscriber');

        $this->className = $className;
        $this->messages = [];

        $event->addSubscriber($this);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

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
