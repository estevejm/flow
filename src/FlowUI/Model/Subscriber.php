<?php

namespace FlowUI\Model;

class Subscriber extends Node
{
    use CanTriggerMessages;

    /**
     * @param string $id
     * @param string $className
     * @param Event $event
     */
    public function __construct($id, $className, Event $event)
    {
        parent::__construct($id, $className, Node::TYPE_SUBSCRIBER);

        $event->addSubscriber($this);
    }
}
