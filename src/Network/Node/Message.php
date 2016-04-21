<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;

class Message extends Node
{
    /**
     * @var Set
     */
    private $publishers;

    /**
     * @param string $id
     * @param string $className
     * @param string $type
     */
    public function __construct($id, $className, $type)
    {
        parent::__construct($id, $className, $type);

        $this->publishers = new Set();
    }

    /**
     * @param MessagePublisher $publisher
     * @return Message
     */
    public function isPublishedBy(MessagePublisher $publisher)
    {
        if (!$this->publishers->has($publisher->getId())) {
            $this->publishers->add($publisher->getId(), $publisher);
            $publisher->addMessage($this);
        }

        return $this;
    }

    /**
     * @return MessagePublisher[]
     */
    public function getPublishers()
    {
        return $this->publishers->toArray();
    }
}
