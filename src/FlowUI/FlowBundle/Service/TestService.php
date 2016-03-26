<?php

namespace FlowUI\FlowBundle\Service;

use Exception;
use FlowUI\Component\Parser\Parser;
use FlowUI\Model\Command;
use FlowUI\Model\Event;
use FlowUI\Model\Handler;
use FlowUI\Model\Subscriber;
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
        $commands = $events = $handlers = $subscribers = [];

        foreach ($this->handlersMap as $commandId => $handlerData) {
            $command = new Command($commandId);
            $handlers[] = new Handler($handlerData['id'], $handlerData['class'], $command);
            $commands[$command->getId()] = $command;
        }

        foreach ($this->subscribersMap as $eventId => $eventSubscribers) {
            $event = new Event($eventId);

            $newSubscribers = array_map(
                function($subscriber) use ($event) {
                    return new Subscriber($subscriber['id'], $subscriber['class'], $event);
                },
                $eventSubscribers
            );

            $subscribers = array_merge($subscribers, $newSubscribers);

            $events[$event->getId()] = $event;
        }

        /** @var Command $command */
        foreach ($commands as $command) {
            $handler = $command->getHandler();
            $messages = $this->getMessagesUsedInClass($handler->getClassName());

            foreach ($messages as $messageId) {
                if (!empty($commands[$messageId])) {
                    $handler->addMessage($commands[$messageId]);
                    //var_dump("warning: you're triggering a command inside a command handler!");
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

        return array_merge($commands, $handlers, $events, $subscribers);
    }

    /**
     * @param string $className
     * @return array
     */
    private function getMessagesUsedInClass($className) {
        return (new Parser())->parse($className);
    }
}
