<?php

namespace FlowUI\FlowBundle\Model;

class Handler extends Node
{

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @param string $id
     * @param Command $command
     */
    public function __construct($id, Command $command)
    {
        parent::__construct($id, 'handler');

        $this->events = [];

        $command->setHandler($this);
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }
}
 