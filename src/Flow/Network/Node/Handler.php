<?php

namespace Flow\Network\Node;

use Flow\Network\Node;

class Handler extends MessagePublisher
{
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
 