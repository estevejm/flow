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
     */
    public function __construct($id)
    {
        parent::__construct($id, null, Node::TYPE_COMMAND);
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
 