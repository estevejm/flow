<?php

namespace EJM\Flow\Tests\Unit\Collector\Reader;

use EJM\Flow\Collector\Reader\SourceCodeReader;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class SourceCodeReaderTest extends PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $className = get_class($this);
        $expectedFilename = (new ReflectionClass($className))->getFileName();

        $fileReader = $this->getFileReaderMock();

        $fileReader->expects($this->once())
            ->method('read')
            ->with($expectedFilename);

        $sourceCodeReader = new SourceCodeReader($fileReader);
        $sourceCodeReader->read($className);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testReadWithUnexistingClass()
    {
        $fileReader = $this->getFileReaderMock();

        $fileReader->expects($this->never())
            ->method('read');

        $sourceCodeReader = new SourceCodeReader($fileReader);
        $sourceCodeReader->read('\Unexisting\Class');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFileReaderMock()
    {
        return $this->getMock('\EJM\Flow\Collector\Reader\FileReader');
    }
}
