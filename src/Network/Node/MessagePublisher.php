<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;

class MessagePublisher extends Node
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
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages->toArray();
    }

    /**
     * @param Message $message
     * @return $this
     */
    public function addMessage(Message $message)
    {
        if (!$this->messages->has($message->getId())) {
            $this->messages->add($message->getId(), $message);
        }

        $message->isPublishedBy($this);

        return $this;
    }
}
