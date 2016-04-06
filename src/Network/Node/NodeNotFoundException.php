<?php

namespace Network\Node;

use RuntimeException;

class NodeNotFoundException extends RuntimeException
{
    public function __construct($class, $id)
    {
        parent::__construct(sprintf('Node of type \'%s\' with id \'%s\' not found', $class, $id));
    }
}
