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
            $messages = $this->getMessagesUsedInClass($handler->getClassName());

            foreach ($messages as $messageId) {
                if (!empty($commands[$messageId])) {
                    $handler->addMessage($commands[$messageId]);
                    var_dump("warning: you're triggering a command inside a command handler!");
                    continue;
                }

                if (empty($events[$messageId])) {
                    $events[$messageId] = new Event($messageId);
                }
                $handler->addMessage($events[$messageId]);
            }
        }

        /** @var Event $event */
        foreach ($events as $event) {
            foreach ($event->getSubscribers() as $subscriber) {
                $messages = $this->getMessagesUsedInClass($subscriber->getClassName());

                foreach ($messages as $messageId) {
                    if (!empty($commands[$messageId])) {
                        $subscriber->addMessage($commands[$messageId]);

                        // if it's a command, it's not an event, moving on ( as we don't want to create an entry for a false event ...
                        continue;
                    }

                    if (empty($events[$messageId])) {
                        // ... here)
                        $events[$messageId] = new Event($messageId);
                    }

                    $subscriber->addMessage($events[$messageId]);
                }
            }

        }

        return array_merge($commands, $events);
    }

    /**
     * @param string $className
     * @return array
     */
    private function getMessagesUsedInClass($className) {
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
