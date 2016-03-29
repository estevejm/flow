<?php

namespace EJM\Flow\Validator\Constraint;

use EJM\Flow\Validator\Constraint;
use EJM\Flow\Validator\Violation;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node;

class EventWithoutSubscriber implements Constraint
{
    /**
     * {@inheritdoc
     */
    public function supportsNode(Node $node)
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
                    sprintf('There is no subscribers for the event \'%s\'.', $node->getId()),
                    Violation::NOTICE
                )
            ];
        }

        return [];
    }
}
 