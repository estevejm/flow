<?php

namespace EJM\Flow\Tests\Collector\Reader;

use EJM\Flow\Collector\Reader\FileReader;
use EJM\Flow\Collector\Reader\SourceCodeReader;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class SourceCodeReaderTest extends PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $expectedFilename = (new ReflectionClass(self::class))->getFileName();

        $fileReader = $this->getMock(FileReader::class);

        $fileReader->expects($this->once())
            ->method('read')
            ->with($expectedFilename);

        $sourceCodeReader = new SourceCodeReader($fileReader);
        $sourceCodeReader->read(self::class);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testReadWithUnexistingClass()
    {
        $fileReader = $this->getMock(FileReader::class);

        $fileReader->expects($this->never())
            ->method('read');

        $sourceCodeReader = new SourceCodeReader($fileReader);
        $sourceCodeReader->read('\Unexisting\Class');
    }
}
