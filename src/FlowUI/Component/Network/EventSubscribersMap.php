<?php

namespace FlowUI\Component\Network;

use FlowUI\Model\Node\Event;
use FlowUI\Model\Node\Subscriber;

class EventSubscribersMap
{
    /**
     * @var array
     */
    private $callableMap;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @var Subscriber[]
     */
    private $subscribers;

    /**
     * @param array $callableMap
     */
    public function __construct(array $callableMap)
    {
        $this->callableMap = $callableMap;
        $this->events = [];
        $this->subscribers = [];

        $this->build();
    }

    private function build()
    {
        foreach ($this->callableMap as $eventId => $eventSubscribers) {
            $event = new Event($eventId);

            $newSubscribers = array_map(
                function($subscriber) use ($event) {
                    return new Subscriber($subscriber['id'], $subscriber['class'], $event);
                },
                $eventSubscribers
            );

            $this->subscribers = array_merge($this->subscribers, $newSubscribers);

            $this->events[$event->getId()] = $event;
        }
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return Subscriber[]
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }
}
