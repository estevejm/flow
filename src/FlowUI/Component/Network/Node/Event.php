<?php

namespace FlowUI\Component\Network\Node;

use FlowUI\Component\Network\Node;

class Event extends Node implements Message
{

    /**
     * @var Subscriber[]
     */
    private $subscribers;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        parent::__construct($id, null, Node::TYPE_EVENT);

        $this->subscribers = [];
    }

    /**
     * @return Subscriber[]
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param Subscriber $subscriber
     */
    public function addSubscriber(Subscriber $subscriber)
    {
        $this->subscribers[] = $subscriber;
    }
}
 