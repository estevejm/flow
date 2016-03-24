<?php

namespace FlowUI\FlowBundle\Model;

class Handler extends Node
{
    use CanTriggerMessages;

    /**
     * @var string
     */
    private $className;

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

        parent::__construct($id, 'handler');

        $this->className = $className;

        $command->setHandler($this);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
 