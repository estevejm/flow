<?php

namespace EJM\Flow\Tests\Unit\Collector;

use EJM\Flow\Collector\Collector;
use EJM\Flow\Collector\Parser\DataCollectorNodeVisitor;
use PhpParser\Parser;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class CollectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $parser;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    /**
     * @var Collector
     */
    private $collector;

    /**
     * @var DataCollectorNodeVisitor
     */
    private $visitor;

    protected function setUp()
    {
        $this->parser = $this->getMock('\PhpParser\Parser');
        $this->visitor = $this->getMock('\EJM\Flow\Collector\Parser\DataCollectorNodeVisitor');
        $this->reader = $this->getMockBuilder('\EJM\Flow\Collector\Reader\SourceCodeReader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->collector = new Collector($this->visitor);
        $this->collector->setParser($this->parser);
        $this->collector->setSourceCodeReader($this->reader);
    }

    public function testCollectWithExistingClass()
    {
        $className = get_class($this);
        $sourceCode = '<?php class Test {}';
        $nodes = ['node1', 'node2'];
        $expectedData = 'some useful data';

        $this->reader->expects($this->once())
            ->method('read')
            ->with($className)
            ->willReturn($sourceCode);

        $this->parser->expects($this->once())
            ->method('parse')
            ->with($sourceCode)
            ->willReturn($nodes);

        $this->visitor->expects($this->once())
            ->method('getData')
            ->willReturn($expectedData);

        $data = $this->collector->collect($className);

        $this->assertEquals($expectedData, $data);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testCollectWithUnexistingClass()
    {
        $this->reader->expects($this->never())
            ->method('read');

        $this->collector->collect('\Unexisting\Class');
    }
}
