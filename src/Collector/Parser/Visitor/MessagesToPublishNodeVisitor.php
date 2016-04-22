<?php

namespace EJM\Flow\Collector\Parser\Visitor;

use EJM\Flow\Collector\Parser\DataCollectorNodeVisitor;
use PhpParser\Node;
use ReflectionClass;

class MessagesToPublishNodeVisitor extends DataCollectorNodeVisitor
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

    /**
     * {@inheritdoc}
     */
    public function beforeTraverse(array $nodes)
    {
        $this->namespace = '';
        $this->uses = [];
        $this->messages = [];
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        // todo: refactor this
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->namespace = $node->name->toString();
        } elseif ($node instanceof Node\Stmt\Use_) {
            $use = $node->uses[0];
            $this->uses[$use->alias] = $use->name->toString();
        } elseif ($node instanceof Node\Expr\New_) {
            if ($node->class->isRelative()) {
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
    private function getMessageIdOfClass($className)
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Invalid class $className.");
        }
        $class = new ReflectionClass($className);
        if ($class->implementsInterface('\SimpleBus\Message\Name\NamedMessage')) {
            return call_user_func([$className, 'messageName']);
        }

        return;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array_unique($this->messages);
    }
}
