<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;

class Subscriber extends MessagePublisher
{
    /**
     * @var Set
     */
    private $events;

    /**
     * @param string $id
     * @param string $className
     */
    public function __construct($id, $className = null)
    {
        parent::__construct($id, $className, Node::TYPE_SUBSCRIBER);

        $this->events = new Set();
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function subscribesTo(Event $event)
    {
        if (!$this->events->has($event->getId())) {
            $this->events->add($event->getId(), $event);
            $event->addSubscriber($this);
        }

        return $this;
    }

    /**
     * @return Event[]
     */
    public function getEventsSubscribedTo()
    {
        return $this->events->getAll();
    }
}
