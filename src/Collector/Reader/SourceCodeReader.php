<?php

namespace EJM\Flow\Collector\Reader;

use Assert\Assertion;
use ReflectionClass;

class SourceCodeReader implements Reader
{
    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @param FileReader $fileReader
     */
    public function __construct(FileReader $fileReader)
    {
        $this->fileReader = $fileReader;
    }

    /**
     * @param string $className
     * @return string
     */
    public function read($className)
    {
        Assertion::classExists($className);

        $filename = (new ReflectionClass($className))->getFileName();

        return $this->fileReader->read($filename);
    }
}
