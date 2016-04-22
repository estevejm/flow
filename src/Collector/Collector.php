<?php

namespace EJM\Flow\Collector;

use Assert\Assertion;
use EJM\Flow\Collector\Reader\FileReader;
use EJM\Flow\Collector\Reader\Reader;
use EJM\Flow\Collector\Reader\SourceCodeReader;
use EJM\Flow\Collector\Parser\DataCollectorNodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;

abstract class Collector
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var DataCollectorNodeVisitor
     */
    private $visitor;

    public function __construct()
    {
        $this->parser = $this->getDefaultParser();
        $this->reader = $this->getDefaultReader();
        $this->visitor = $this->getVisitor();

        Assertion::isInstanceOf($this->visitor, '\EJM\Flow\Collector\Parser\DataCollectorNodeVisitor');
    }

    /**
     * @return Parser
     */
    private function getDefaultParser()
    {
        return (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @return Reader
     */
    private function getDefaultReader()
    {
        return new SourceCodeReader(new FileReader());
    }

    /**
     * @return DataCollectorNodeVisitor
     */
    protected abstract function getVisitor();

    /**
     * @param string $className
     * @return array
     */
    public function collect($className)
    {
        Assertion::classExists($className);

        $sourceCode = $this->reader->read($className);
        $nodes = $this->parser->parse($sourceCode);

        return $this->collectData($nodes);
    }

    /**
     * @param $nodes
     * @return mixed
     */
    private function collectData($nodes)
    {
        $this->traverseNodes($nodes);

        return $this->visitor->getData();
    }

    /**
     * @param $nodes
     */
    private function traverseNodes($nodes)
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->visitor);
        $traverser->traverse($nodes);
    }

    /**
     * @param Parser $parser
     * @return $this
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @param Reader $reader
     * @return $this
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * @param DataCollectorNodeVisitor $visitor
     */
    public function setVisitor(DataCollectorNodeVisitor $visitor)
    {
        $this->visitor = $visitor;
    }
}
