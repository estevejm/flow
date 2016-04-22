<?php

namespace EJM\Flow\Collector;

use Assert\Assertion;
use EJM\Flow\Collector\Reader\FileReader;
use EJM\Flow\Collector\Reader\SourceCodeReader;
use EJM\Flow\Collector\Parser\DataCollectorNodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class Collector
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var SourceCodeReader
     */
    private $sourceCodeReader;

    /**
     * @var DataCollectorNodeVisitor
     */
    private $visitor;

    /**
     * @param DataCollectorNodeVisitor $visitor
     */
    public function __construct(DataCollectorNodeVisitor $visitor)
    {
        $this->parser = $this->getDefaultParser();
        $this->sourceCodeReader = $this->getDefaultReader();
        $this->visitor = $visitor;
    }

    /**
     * @param string $className
     * @return array
     */
    public function collect($className)
    {
        Assertion::classExists($className);
        Assertion::notNull($this->visitor);

        $sourceCode = $this->sourceCodeReader->read($className);
        $nodes = $this->parser->parse($sourceCode);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->visitor);
        $traverser->traverse($nodes);

        return $this->visitor->getData();
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
     * @param SourceCodeReader $sorceCodeReader
     * @return $this
     */
    public function setSourceCodeReader(SourceCodeReader $sorceCodeReader)
    {
        $this->sourceCodeReader = $sorceCodeReader;

        return $this;
    }

    /**
     * @return Parser
     */
    private function getDefaultParser()
    {
        return (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @return SourceCodeReader
     */
    private function getDefaultReader()
    {
        return new SourceCodeReader(new FileReader());
    }
}
