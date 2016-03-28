<?php

namespace Flow\Parser;

use Exception;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;
use ReflectionClass;

class Parser
{
    /**
     * @var ParserFactory
     */
    private $factory;

    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * @var DataCollectorNodeVisitor
     */
    private $visitor;

    /**
     * @param ParserFactory $factory
     * @param NodeTraverser $traverser
     */
    public function __construct(ParserFactory $factory, NodeTraverser $traverser)
    {
        $this->factory = $factory;
        $this->traverser = $traverser;
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
    public function parse($className)
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("$className not defined.");
        }

        $parser = $this->getParser();
        $sourceCode = $this->getSourceCode($className);

        try {
            $nodes = $parser->parse($sourceCode);

            $this->traverser->traverse($nodes);

            return $this->visitor->getData();

        } catch (Exception $e) {
            echo 'Parse Error: ', $e->getMessage();
        }

        return [];
    }

    /**
     * @return \PhpParser\Parser
     */
    private function getParser()
    {
        return $this->factory->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @param string $className
     * @return string
     */
    private function getSourceCode($className)
    {
        $class = new ReflectionClass($className);
        $fileName = $class->getFileName();
        $code = file_get_contents($fileName);

        return $code;
    }
}
