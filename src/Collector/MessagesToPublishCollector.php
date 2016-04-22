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
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser();
        $this->sourceCodeReader = new SourceCodeReader(new FileReader());

        $this->setVisitor($visitor);
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

        $this->traverser->traverse($nodes);

        return $this->visitor->getData();
    }

    /**
     * @param DataCollectorNodeVisitor $visitor
     * @return $this
     */
    public function setVisitor(DataCollectorNodeVisitor $visitor)
    {
        if ($this->visitor instanceof DataCollectorNodeVisitor) {
            $this->traverser->removeVisitor($this->visitor);
        }

        $this->visitor = $visitor;
        $this->traverser->addVisitor($this->visitor);

        return $this;
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
}
