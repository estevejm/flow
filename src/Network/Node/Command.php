<?php

namespace EJM\Flow\Network\Node;

use EJM\Flow\Network\Node;

class Command extends Node implements Message
{

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @param string $id
     * @param Handler $handler
     */
    public function __construct($id, Handler $handler)
    {
        parent::__construct($id, null, Node::TYPE_COMMAND);

        $handler->handles($this);

        $this->handler = $handler;
    }

    /**
     * @return Handler
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
 