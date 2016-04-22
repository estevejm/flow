<?php

namespace EJM\Flow\Collector;

use Assert\Assertion;
use EJM\Flow\Collector\Reader\SourceCodeReader;
use EJM\Flow\Collector\Parser\DataCollectorNodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;

class MessagesToPublishCollector
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * @var SourceCodeReader
     */
    private $sorceCodeReader;

    /**
     * @var DataCollectorNodeVisitor
     */
    private $visitor;

    /**
     * @param Parser $parser
     * @param NodeTraverser $traverser
     * @param SourceCodeReader $sorceCodeReader
     */
    public function __construct(Parser $parser, NodeTraverser $traverser, SourceCodeReader $sorceCodeReader)
    {
        $this->parser = $parser;
        $this->traverser = $traverser;
        $this->sorceCodeReader = $sorceCodeReader;
    }

    /**
     * @param DataCollectorNodeVisitor $visitor
     */
    public function setVisitor(DataCollectorNodeVisitor $visitor)
    {
        if ($this->visitor instanceof DataCollectorNodeVisitor) {
            $this->traverser->removeVisitor($this->visitor);
        }

        $this->visitor = $visitor;
        $this->traverser->addVisitor($this->visitor);
    }

    /**
     * @param string $className
     * @return array
     */
    public function collect($className)
    {
        Assertion::classExists($className);
        Assertion::notNull($this->visitor);

        $sourceCode = $this->sorceCodeReader->read($className);
        $nodes = $this->parser->parse($sourceCode);

        $this->traverser->traverse($nodes);

        return $this->visitor->getData();
    }
}
