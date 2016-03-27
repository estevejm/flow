<?php

namespace FlowUI\Model\Node;

use FlowUI\Model\Node;

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
 