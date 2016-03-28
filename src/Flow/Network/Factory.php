<?php

namespace Flow\Network;

use Flow\Parser\Parser;
use Flow\Network\Node\Command;
use Flow\Network\Node\Event;
use Flow\Network\Node\Handler;
use Flow\Network\Node\Subscriber;

class Factory
{
    /**
     * @var $parser
     */
    private $parser;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param Map $map
     * @return Network
     */
    public function create(Map $map)
    {
        $blueprint = new Blueprint();

        foreach ($map->getCommandHandlerMap() as $commandId => $handlerData) {
            $blueprint->addCommand(new Command($commandId));
            $blueprint->addMessagePublisher(
                new Handler($handlerData['id'], $handlerData['class'], $blueprint->getCommand($commandId))
            );
        }

        foreach ($map->getEventSubscribersMap() as $eventId => $eventSubscribers) {
            $blueprint->addEvent(new Event($eventId));
            foreach ($eventSubscribers as $subscriberData) {
                $blueprint->addMessagePublisher(
                    new Subscriber($subscriberData['id'], $subscriberData['class'], $blueprint->getEvent($eventId))
                );
            }
        }

        foreach($this->getPublisherMessagesMap($map) as $messagePublisherId => $messageIds) {
            $messagePublisher = $blueprint->getMessagePublisher($messagePublisherId);
            foreach ($messageIds as $messageId) {
                if ($blueprint->hasCommand($messageId)) {
                    $messagePublisher->addMessage($blueprint->getCommand($messageId));
                    continue;
                }

                if (!$blueprint->hasEvent($messageId)) {
                    $blueprint->addEvent(new Event($messageId));
                }

                $messagePublisher->addMessage($blueprint->getEvent($messageId));
            }

        }

        return new Network($blueprint->getNodes());
    }

    /**
     * @param Map $map
     * @return array
     */
    private function getPublisherMessagesMap(Map $map)
    {
        $publisherMessagesMap = [];

        foreach ($map->getCommandHandlerMap() as $commandId => $handlerData) {
            $publisherMessagesMap[$handlerData['id']] = $this->getIdsOfMessagesInClass($handlerData['class']);
        }

        foreach ($map->getEventSubscribersMap() as $eventId => $eventSubscribers) {
            foreach ($eventSubscribers as $subscriberData) {
                $publisherMessagesMap[$subscriberData['id']] = $this->getIdsOfMessagesInClass($subscriberData['class']);
            }
        }

        return $publisherMessagesMap;
    }

    /**
     * @param string $className
     * @return array
     */
    private function getIdsOfMessagesInClass($className) {
        return $this->parser->parse($className);
    }
}
