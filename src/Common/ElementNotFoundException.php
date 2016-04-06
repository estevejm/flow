<?php

namespace EJM\Flow\Common;

use RuntimeException;

class ElementNotFoundException extends RuntimeException
{
    public function __construct($id)
    {
        parent::__construct(sprintf('Element with id \'%s\' not found', $id));
    }
}
