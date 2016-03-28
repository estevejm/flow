<?php

namespace Flow\Collector\Reader;

use ReflectionClass;

class SourceCodeReader
{
    /**
     * @param string $className
     * @return string
     */
    public function read($className)
    {
        // todo: check class name ??
        $class = new ReflectionClass($className);
        $fileName = $class->getFileName();

        return file_get_contents($fileName); // todo: check filesystem abstraction
    }
}
