<?php

namespace FlowUI\Component\Validator\Constraint;

use FlowUI\Component\Validator\Constraint;
use FlowUI\Component\Validator\Violation;
use FlowUI\FlowBundle\Model\Event;
use FlowUI\FlowBundle\Model\Node;

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
        if (count($node->getSubscribers()) > 0) {
            return [new Violation($node, 'Event without any subscriber.', Violation::NOTICE)];
        }

        return [];
    }
}
 