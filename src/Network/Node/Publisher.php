<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;

class Publisher extends Node
{
    /**
     * @var Set
     */
    private $messages;

    /**
     * @param string $id
     * @param string $className
     * @param string $type
     */
    public function __construct($id, $className, $type)
    {
        parent::__construct($id, $className, $type);

        $this->messages = new Set();
    }

    /**
     * @param Message $message
     * @return $this
     */
    public function publishes(Message $message)
    {
        if (!$this->messages->has($message->getId())) {
            $this->messages->add($message->getId(), $message);
        }

        $message->isBeingPublishedBy($this);

        return $this;
    }

    /**
     * @return Message[]
     */
    public function getMessagesToPublish()
    {
        return $this->messages->toArray();
    }
}
