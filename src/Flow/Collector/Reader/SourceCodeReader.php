<?php

namespace Flow\Collector\Reader;

use Assert\Assertion;
use ReflectionClass;

class SourceCodeReader
{
    /**
     * @param string $className
     * @return string
     */
    public function read($className)
    {
        Assertion::classExists($className);

        $class = new ReflectionClass($className);
        $fileName = $class->getFileName();

        return file_get_contents($fileName); // todo: check filesystem abstraction
    }
}
