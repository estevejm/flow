<?php

namespace FlowUI\FlowBundle\Service;

use Exception;
use FlowUI\FlowBundle\Model\Command;
use FlowUI\FlowBundle\Model\Event;
use FlowUI\FlowBundle\Model\Handler;
use FlowUI\FlowBundle\Model\Subscriber;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionClass;
use SimpleBus\Message\Name\NamedMessage;

class TestService
{
    /**
     * @var array
     */
    private $handlersMap;

    /**
     * @var array
     */
    private $subscribersMap;

    /**
     * @param array $handlersMap
     * @param array $subscribersMap
     */
    public function __construct(array $handlersMap, array $subscribersMap)
    {
        $this->handlersMap = $handlersMap;
        $this->subscribersMap = $subscribersMap;
    }

    public function build()
    {
        $commands = $events = [];

        foreach ($this->handlersMap as $commandId => $handlerData) {
            $command = new Command($commandId);
            new Handler($handlerData['id'], $handlerData['class'], $command);
            $commands[$command->getId()] = $command;
        }

        foreach ($this->subscribersMap as $eventId => $subscribers) {
            $event = new Event($eventId);

            array_map(
                function($subscriber) use ($event) {
                    return new Subscriber($subscriber['id'], $subscriber['class'], $event);
                },
                $subscribers
            );

            $events[$event->getId()] = $event;
        }

        /** @var Command $command */
        foreach ($commands as $command) {
            $handler = $command->getHandler();
            $handlerEventIds = $this->getEventsTriggeredByHandler($handler->getClassName());

            foreach ($handlerEventIds as $subscriberMessageId) {
                if (!empty($commands[$subscriberMessageId])) {
                    var_dump("warning: you're triggering a command inside a command handler!");
                }

                if (empty($events[$subscriberMessageId])) {
                    $events[$subscriberMessageId] = new Event($subscriberMessageId);
                }
                $handler->addEvent($events[$subscriberMessageId]);
            }
        }

        /** @var Event $event */
        foreach ($events as $event) {
            foreach ($event->getSubscribers() as $subscriber) {
                $subscriberMessageIds = $this->getEventsTriggeredByHandler($subscriber->getClassName());

                foreach ($subscriberMessageIds as $subscriberMessageId) {
                    if (!empty($commands[$subscriberMessageId])) {
                        $subscriber->addCommand($commands[$subscriberMessageId]);

                        // if it's a command, it's not an event, moving on... (as we don't want to create false events ...
                        continue;
                    }

                    if (empty($events[$subscriberMessageId])) {
                        // ... here)
                        $events[$subscriberMessageId] = new Event($subscriberMessageId);
                    }

                    $subscriber->addEvent($events[$subscriberMessageId]);
                }
            }

        }

        return array_merge($commands, $events);
    }

    /**
     * @param string $className
     * @return array
     */
    private function getEventsTriggeredByHandler($className) {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Invalid class $className.");
        }
        $class = new ReflectionClass($className);
        $fileName = $class->getFileName();
        $code = file_get_contents($fileName);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();

        $visitor = new RecordedEventsNodeVisitor();
        $traverser->addVisitor($visitor);

        try {
            $stmts = $parser->parse($code);
            $stmts = $traverser->traverse($stmts);

            //var_dump($stmts);
            return $visitor->getEventIds();


        } catch (Exception $e) {
            echo 'Parse Error: ', $e->getMessage();
        }

        return [];
    }
}
