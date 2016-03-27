<?php

namespace FlowUI\Component\Network;

use FlowUI\Component\Parser\Parser;
use FlowUI\Model\Node\Command;
use FlowUI\Model\Node\Event;
use FlowUI\Model\Node\Handler;
use FlowUI\Model\Node\Subscriber;

class Factory
{
    /**
     * @var array
     */
    private $commandHandlerMap;

    /**
     * @var array
     */
    private $eventSubscribersMap;

    /**
     * @var $parser
     */
    private $parser;

    /**
     * @param array $commandHandlerMap
     * @param array $eventSubscribersMap
     * @param Parser $parser
     */
    public function __construct(array $commandHandlerMap, array $eventSubscribersMap, Parser $parser)
    {
        $this->commandHandlerMap = $commandHandlerMap;
        $this->eventSubscribersMap = $eventSubscribersMap;
        $this->parser = $parser;
    }

    /**
     * @return Network
     */
    public function create()
    {
        $commands = [];
        $handlers = [];
        $events = [];
        $subscribers = [];

        foreach ($this->commandHandlerMap as $commandId => $handlerData) {
            $command = new Command($commandId);
            $handlers[$handlerData['id']] = new Handler($handlerData['id'], $handlerData['class'], $command);
            $commands[$command->getId()] = $command;
        }

        foreach ($this->eventSubscribersMap as $eventId => $eventSubscribers) {
            $event = new Event($eventId);

            foreach ($eventSubscribers as $subscriber) {
                $subscribers[$subscriber['id']] = new Subscriber($subscriber['id'], $subscriber['class'], $event);
            }

            $events[$event->getId()] = $event;
        }

        foreach ($handlers as $handler) {
            $messages = $this->getMessagesUsedInClass($handler->getClassName());

            foreach ($messages as $messageId) {
                if (!empty($commands[$messageId])) {
                    $handler->addMessage($commands[$messageId]);
                    continue;
                }

                if (empty($events[$messageId])) {
                    $events[$messageId] = new Event($messageId);
                }

                $handler->addMessage($events[$messageId]);
            }
        }

        foreach ($subscribers as $subscriber) {
            $messages = $this->getMessagesUsedInClass($subscriber->getClassName());

            foreach ($messages as $messageId) {
                if (!empty($commands[$messageId])) {
                    $subscriber->addMessage($commands[$messageId]);
                    continue;
                }

                if (empty($events[$messageId])) {
                    $events[$messageId] = new Event($messageId);
                }

                $subscriber->addMessage($events[$messageId]);
            }
        }

        return new Network($commands, $handlers, $events, $subscribers);
    }

    /**
     * @param string $className
     * @return array
     */
    private function getMessagesUsedInClass($className) {
        return $this->parser->parse($className);
    }
}
