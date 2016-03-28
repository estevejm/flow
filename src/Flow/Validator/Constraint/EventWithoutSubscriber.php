<?php

namespace Flow\Validator\Constraint;

use Flow\Validator\Constraint;
use Flow\Validator\Violation;
use Flow\Network\Node\Event;
use Flow\Network\Node;

class EventWithoutSubscriber implements Constraint
{
    /**
     * {@inheritdoc
     */
    public function supportNode(Node $node)
    {
        return $node instanceof Event;
    }

    /**
     * @param Event $node
     * @return Violation[]
     */
    public function validate(Node $node)
    {
        if (count($node->getSubscribers()) === 0) {
            return [
                new Violation(
                    $node,
                    sprintf('There is no subscribers for the event \'%s\'', $node->getId()),
                    Violation::NOTICE
                )
            ];
        }

        return [];
    }
}
 