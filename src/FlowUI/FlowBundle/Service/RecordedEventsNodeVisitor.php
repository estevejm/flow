<?php

namespace FlowUI\FlowBundle\Service;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use ReflectionClass;
use SimpleBus\Message\Name\NamedMessage;

class RecordedEventsNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $classMap;

    /**
     * @var array
     */
    private $events;

    public function __construct()
    {
        $this->classMap = [];
        $this->events = [];
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node) {
        if ($node instanceof Node\Stmt\Use_) {
            $use = $node->uses[0];
            $this->classMap[$use->alias] = $use->name->toString();
        } elseif ($node instanceof Node\Expr\New_ || $node instanceof Node\Expr\StaticCall) {
            if ($node->class->isRelative()) {
                // don't know how to reproduce
                var_dump("relative class!");
                $eventClass = $node->class->toString();
            } elseif ($node->class->isQualified()) {
                $eventClass = $node->class->getFirst();
                if (empty($this->classMap[$eventClass])) {
                    throw new \Exception('Qualified class could not be resolved');
                }
                $eventClass = $this->classMap[$eventClass] . '\\' . $node->class->getLast();
            } elseif ($node->class->isUnqualified()) {
                $eventClass = $node->class->toString();
                if (empty($this->classMap[$eventClass])) {
                    throw new \Exception('Unqualified class could not be resolved');
                }
                $eventClass = $this->classMap[$eventClass];
            } elseif ($node->class->isFullyQualified()) {
                $eventClass = $node->class->toString();
            } else {
                // should not be possible
                $eventClass = $node->class->toString();
            }

            $eventId = $this->getEventIdOfClass($eventClass);

            if ($eventId) {
                $this->events[] = $eventId;
            }
        }
    }

    /**
     * @param $className
     * @return mixed
     */
    private function getEventIdOfClass($className){
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
    public function getEventIds()
    {
        return array_unique($this->events);
    }
}
