<?php

namespace EJM\Flow\Tests\Collector;

use EJM\Flow\Collector\Collector;
use EJM\Flow\Collector\Parser\DataCollectorNodeVisitor;
use EJM\Flow\Collector\Reader\SourceCodeReader;
use PhpParser\NodeTraverser;
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
        $this->parser = $this->getMock(Parser::class);
        $this->traverser = $this->getMock(NodeTraverser::class);
        $this->reader = $this->getMockBuilder(SourceCodeReader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collector = new Collector($this->parser, $this->traverser, $this->reader);
    }

    public function testSetVisitor()
    {
        $firstVisitor = $this->getMock(DataCollectorNodeVisitor::class);
        $secondVisitor = $this->getMock(DataCollectorNodeVisitor::class);

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
        $className = self::class;
        $sourceCode = '<?php class Test {}';
        $nodes = ['node1', 'node2'];
        $expectedData = 'some useful data';

        $visitor = $this->getMock(DataCollectorNodeVisitor::class);

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
        $visitor = $this->getMock(DataCollectorNodeVisitor::class);

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

        $this->collector->collect(self::class);
    }
}
