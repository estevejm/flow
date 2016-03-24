<?php

namespace FlowUI\FlowBundle\Model;

class Subscriber extends Node
{

    /**
     * @var Command[]
     */
    private $commands;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @param string $id
     * @param Event $event
     */
    public function __construct($id, Event $event)
    {
        parent::__construct($id, 'subscriber');

        $this->commands = [];
        $this->events = [];

        $event->addSubscriber($this);
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param Event $event
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;
    }
}
 