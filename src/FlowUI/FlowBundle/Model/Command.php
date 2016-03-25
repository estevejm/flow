<?php

namespace FlowUI\FlowBundle\Model;

class Command extends Node implements Message
{

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        // todo: get classname of command
        parent::__construct($id, null, 'command');
    }

    /**
     * @return Handler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param Handler $handler
     */
    public function setHandler(Handler $handler)
    {
        $this->handler = $handler;
    }
}
 