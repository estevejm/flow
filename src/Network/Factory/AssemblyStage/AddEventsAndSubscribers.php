<?php

namespace EJM\Flow\Network\Factory\AssemblyStage;

use Assert;
use EJM\Flow\Network\Blueprint;
use EJM\Flow\Network\Factory\AssemblyStage;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Subscriber;

class AddEventsAndSubscribers implements AssemblyStage
{
    /**
     * @var array
     */
    private $eventSubscribersMap;

    /**
     * @param array $commandHandlerMap
     */
    public function __construct(array $commandHandlerMap)
    {
        foreach ($commandHandlerMap as $event => $subscribers) {
            Assert\that($event)->string();
            Assert\that($subscribers)->isArray();
            foreach ($subscribers as $subscriber) {
                Assert\that($subscriber)->isArray()->keyExists('id')->keyExists('class');
            }
        }

        $this->eventSubscribersMap = $commandHandlerMap;
    }

    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint)
    {
        foreach ($this->eventSubscribersMap as $eventId => $eventSubscribers) {
            $event = new Event($eventId);
            $blueprint->addEvent($event);
            foreach ($eventSubscribers as $subscriberData) {
                $subscriber = $this->findOrCreateSubscriber($blueprint, $subscriberData);
                $subscriber->subscribesTo($event);
                $blueprint->addMessagePublisher($subscriber);
            }
        }
    }

    /**
     * @param Blueprint $blueprint
     * @param array $subscriberData
     * @return Subscriber
     */
    private function findOrCreateSubscriber(Blueprint $blueprint, $subscriberData)
    {
        if ($blueprint->hasMessagePublisher($subscriberData['id'])) {
            return $blueprint->getMessagePublisher($subscriberData['id']);
        }

        return new Subscriber($subscriberData['id'], $subscriberData['class']);
    }
}
