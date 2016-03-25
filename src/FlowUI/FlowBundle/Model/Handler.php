<?php

namespace FlowUI\FlowBundle\Model;

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
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Invalid class $className.");
        }

        parent::__construct($id, $className, 'handler');

        $command->setHandler($this);
    }
}
 