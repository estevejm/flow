<?php

namespace EJM\Flow\Tests\Unit\Collector;

use EJM\Flow\Collector\Collector;
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
    private $traverser;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    /**
     * @var Collector
     */
    private $collector;

    protected function setUp()
    {
        $this->parser = $this->getMock('\PhpParser\Parser');
        $this->traverser = $this->getMock('\PhpParser\NodeTraverser');
        $this->reader = $this->getMockBuilder('\EJM\Flow\Collector\Reader\SourceCodeReader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->collector = new Collector($this->parser, $this->traverser, $this->reader);
    }

    public function testSetVisitor()
    {
        $firstVisitor = $this->getVisitorMock();
        $secondVisitor = $this->getVisitorMock();

        $this->traverser->expects($this->at(0))
            ->method('addVisitor')
            ->with($this->identicalTo($firstVisitor));

        $this->traverser->expects($this->at(1))
            ->method('removeVisitor')
            ->with($this->identicalTo($firstVisitor));

        $this->traverser->expects($this->at(2))
            ->method('addVisitor')
            ->with($this->identicalTo($secondVisitor));

        $this->collector->setVisitor($firstVisitor);
        $this->collector->setVisitor($secondVisitor);
    }

    public function testCollectWithExistingClass()
    {
        $className = get_class($this);
        $sourceCode = '<?php class Test {}';
        $nodes = ['node1', 'node2'];
        $expectedData = 'some useful data';

        $visitor = $this->getVisitorMock();

        $this->collector->setVisitor($visitor);

        $this->reader->expects($this->once())
            ->method('read')
            ->with($className)
            ->willReturn($sourceCode);

        $this->parser->expects($this->once())
            ->method('parse')
            ->with($sourceCode)
            ->willReturn($nodes);

        $this->traverser->expects($this->once())
            ->method('traverse')
            ->with($nodes);

        $visitor->expects($this->once())
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
        $visitor = $this->getVisitorMock();

        $this->collector->setVisitor($visitor);

        $this->reader->expects($this->never())
            ->method('read');

        $this->collector->collect('\Unexisting\Class');
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testCollectWithUndefinedVisitor()
    {
        $this->reader->expects($this->never())
            ->method('read');

        $this->collector->collect(get_class($this));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getVisitorMock()
    {
        return $this->getMock('\EJM\Flow\Collector\Parser\DataCollectorNodeVisitor');
    }
}
