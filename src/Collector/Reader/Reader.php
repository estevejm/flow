<?php

namespace EJM\Flow\Collector\Reader;

interface Reader
{
    /**
     * @param string $className
     * @return string
     */
    public function read($className);
}