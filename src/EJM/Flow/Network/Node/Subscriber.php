<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Network\Node;

class Subscriber extends MessagePublisher
{
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
