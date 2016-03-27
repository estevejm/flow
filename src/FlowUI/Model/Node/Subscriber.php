<?php

namespace FlowUI\Model\Node;

use FlowUI\Model\Node;

class Subscriber extends Node implements MessagePublisher
{
    use CanPublishMessages;

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
