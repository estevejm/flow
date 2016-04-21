<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;

class Handler extends Publisher
{
    /**
     * @var Set
     */
    private $commands;

    /**
     * @param string $id
     * @param string $className
     */
    public function __construct($id, $className = null)
    {
        parent::__construct($id, $className, Node::TYPE_HANDLER);

        $this->commands = new Set();
    }

    /**
     * @param Command $command
     * @return $this
     */
    public function handles(Command $command)
    {
        if (!$this->commands->has($command->getId())) {
            $this->commands->add($command->getId(), $command);
        }

        return $this;
    }

    /**
     * @return Command[]
     */
    public function getCommandsToHandle()
    {
        return $this->commands->toArray();
    }
}
 