<?php

namespace FlowUI\FlowBundle\Model;

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
        // todo: get classname of event
        parent::__construct($id, null, 'event');

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
 