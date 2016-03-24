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
     * @var string
     */
    private $className;

    /**
     * @param string $id
     * @param string $className
     * @param Event $event
     */
    public function __construct($id, $className, Event $event)
    {
        parent::__construct($id, 'subscriber');

        $this->commands = [];
        $this->events = [];
        $this->className = $className;

        $event->addSubscriber($this);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param Command $command
     */
    public function addCommand(Command $command)
    {
        $this->commands[] = $command;
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
