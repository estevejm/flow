<?php

namespace FlowUI\Model;

class Handler extends Node
{
    use CanTriggerMessages;

    /**
     * @param string $id
     * @param string $className
     * @param Command $command
     */
    public function __construct($id, $className, Command $command)
    {
        parent::__construct($id, $className, Node::TYPE_HANDLER);

        $command->setHandler($this);
    }
}
 