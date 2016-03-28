<?php

namespace Flow\Parser;

use Exception;
use Flow\Parser\Visitor\MessagesUsedNodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionClass;

class Parser
{
    /**
     * @param string $className
     * @return array
     */
    public function parse($className) {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Invalid class $className.");
        }
        $class = new ReflectionClass($className);
        $fileName = $class->getFileName();
        $code = file_get_contents($fileName);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();

        $visitor = new MessagesUsedNodeVisitor();
        $traverser->addVisitor($visitor);

        try {
            $stmts = $parser->parse($code);
            $stmts = $traverser->traverse($stmts);

            //var_dump($stmts);
            return $visitor->getMessages();


        } catch (Exception $e) {
            echo 'Parse Error: ', $e->getMessage();
        }

        return [];
    }
}
 