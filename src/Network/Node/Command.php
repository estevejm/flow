<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;

class Command extends Node implements Message
{

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var Set
     */
    private $publishers;

    /**
     * @param string $id
     * @param Handler $handler
     */
    public function __construct($id, Handler $handler)
    {
        parent::__construct($id, null, Node::TYPE_COMMAND);

        $handler->handles($this);

        $this->publishers = new Set();
        $this->handler = $handler;
    }

    /**
     * @return Handler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getPublishers()
    {
        return $this->publishers->toArray();
    }
}
 