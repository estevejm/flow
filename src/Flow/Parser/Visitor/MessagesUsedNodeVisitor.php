<?php

namespace Flow\Parser\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use ReflectionClass;
use SimpleBus\Message\Name\NamedMessage;

class MessagesUsedNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var array
     */
    private $uses;

    /**
     * @var array
     */
    private $messages;

    public function __construct()
    {
        $this->namespace = '';
        $this->uses = [];
        $this->messages = [];
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node) {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->namespace = $node->name->toString();
        } elseif ($node instanceof Node\Stmt\Use_) {
            $use = $node->uses[0];
            $this->uses[$use->alias] = $use->name->toString();
        } elseif ($node instanceof Node\Expr\New_ || $node instanceof Node\Expr\StaticCall) {
            if ($node->class->isRelative()) {
                // don't know how to reproduce
                var_dump("relative class!");
                $eventClass = $node->class->toString();
            } elseif ($node->class->isQualified()) {
                $eventClass = $node->class->getFirst();
                if (empty($this->uses[$eventClass])) {
                    $eventClass = $this->namespace . '\\' . $eventClass;
                } else {
                    $eventClass = $this->uses[$eventClass] . '\\' . $node->class->getLast();
                }
            } elseif ($node->class->isUnqualified()) {
                $eventClass = $node->class->toString();
                if (empty($this->uses[$eventClass])) {
                    $eventClass = $this->namespace . '\\' . $eventClass;
                } else {
                    $eventClass = $this->uses[$eventClass];
                }
            } elseif ($node->class->isFullyQualified()) {
                $eventClass = $node->class->toString();
            } else {
                // should not be possible
                $eventClass = $node->class->toString();
            }

            $messageId = $this->getMessageIdOfClass($eventClass);

            if ($messageId) {
                $this->messages[] = $messageId;
            }
        }
    }

    /**
     * @param $className
     * @return mixed
     */
    private function getMessageIdOfClass($className){
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Invalid class $className.");
        }
        $class = new ReflectionClass($className);
        if ($class->implementsInterface(NamedMessage::class)) {
            return call_user_func([$className, 'messageName']);
        }

        return;
    }
    /**
     * @return array
     */
    public function getMessages()
    {
        return array_unique($this->messages);
    }
}
