<?php

namespace FlowUI\FlowBundle\Model;

class Event extends Node
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
        parent::__construct($id, 'event');

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
 