<?php

namespace FlowUI\FlowBundle\Model;

class Handler extends Node
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var Event[]
     */
    private $messages;

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
        $this->messages = [];

        $command->setHandler($this);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return Message[]
     */
    public function getMessage()
    {
        return $this->messages;
    }

    /**
     * @param Message $message
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
    }
}
 