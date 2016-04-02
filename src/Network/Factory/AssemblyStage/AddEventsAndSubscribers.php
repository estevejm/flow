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
            $blueprint->addEvent(new Event($eventId));
            foreach ($eventSubscribers as $subscriberData) {
                $blueprint->addMessagePublisher(
                    new Subscriber($subscriberData['id'], $subscriberData['class'], $blueprint->getEvent($eventId))
                );
            }
        }
    }
}
