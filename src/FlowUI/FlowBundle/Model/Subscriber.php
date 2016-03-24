<?php

namespace FlowUI\FlowBundle\Model;

class Subscriber extends Node
{
    use CanTriggerMessages;

    /**
     * @var string
     */
    private $className;

    /**
     * @param string $id
     * @param string $className
     * @param Event $event
     */
    public function __construct($id, $className, Event $event)
    {
        parent::__construct($id, 'subscriber');

        $this->className = $className;

        $event->addSubscriber($this);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
