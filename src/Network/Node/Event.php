<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;

class Event extends Node implements Message
{
    /**
     * @var Set
     */
    private $publishers;

    /**
     * @var Set
     */
    private $subscribers;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        parent::__construct($id, null, Node::TYPE_EVENT);

        $this->publishers = new Set();
        $this->subscribers = new Set();
    }

    /**
     * @return Subscriber[]
     */
    public function getSubscribers()
    {
        return $this->subscribers->getAll();
    }

    /**
     * @param Subscriber $subscriber
     * @return $this
     */
    public function addSubscriber(Subscriber $subscriber)
    {
        if (!$this->subscribers->has($subscriber->getId())) {
            $this->subscribers->add($subscriber->getId(), $subscriber);
            $subscriber->subscribesTo($this);
        }

        return $this;
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
        return $this->publishers->getAll();
    }
}
 