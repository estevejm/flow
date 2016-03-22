<?php

namespace FlowUI\FlowBundle\Model;

class Subscriber extends Node
{

    /**
     * @var Command[]
     */
    private $commands;

    /**
     * @param string $id
     * @param Event $event
     */
    public function __construct($id, Event $event)
    {
        parent::__construct($id, 'subscriber');

        $this->commands = [];

        $event->addSubscriber($this);
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }
}
 