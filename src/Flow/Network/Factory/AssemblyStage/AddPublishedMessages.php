<?php

namespace Flow\Network\Factory\AssemblyStage;

use Flow\Collector\Collector;
use Flow\Network\Blueprint;
use Flow\Network\Factory\AssemblyStage;
use Flow\Network\Node\Event;

class AddPublishedMessages implements AssemblyStage
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
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint)
    {
        foreach($blueprint->getMessagePublishers() as $publisher) {
            $messageIds = $this->messagesUsedCollector->collect($publisher->getClassName());
            foreach ($messageIds as $messageId) {
                if ($blueprint->hasCommand($messageId)) {
                    $publisher->addMessage($blueprint->getCommand($messageId));
                    continue;
                }

                if (!$blueprint->hasEvent($messageId)) {
                    $blueprint->addEvent(new Event($messageId));
                }

                $publisher->addMessage($blueprint->getEvent($messageId));
            }
        }
    }
}
