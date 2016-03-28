<?php

namespace Flow\Network;

use Flow\Collector\Collector;
use Flow\Network\Node\Command;
use Flow\Network\Node\Event;
use Flow\Network\Node\Handler;
use Flow\Network\Node\Subscriber;

class Factory
{
    /**
     * @var Collector $messagesUsedCollector
     */
    private $messagesUsedCollector;

    /**
     * @param Collector $messagesUsedCollector
     */
    public function __construct(Collector $messagesUsedCollector)
    {
        $this->messagesUsedCollector = $messagesUsedCollector;
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

        // todo: create middlewares to allow blueprint extension

        return new Network($blueprint->getNodes());
    }

    /**
     * @param Map $map
     * @return array
     */
    private function getPublisherMessagesMap(Map $map)
    {
        $publisherMessagesMap = [];

        foreach ($map->getCommandHandlerMap() as $commandId => $handler) {
            $publisherMessagesMap[$handler['id']] = $this->messagesUsedCollector->collect($handler['class']);
        }

        foreach ($map->getEventSubscribersMap() as $eventId => $subscriberCollection) {
            foreach ($subscriberCollection as $subscriber) {
                $publisherMessagesMap[$subscriber['id']] = $this->messagesUsedCollector->collect($subscriber['class']);
            }
        }

        return $publisherMessagesMap;
    }
}
