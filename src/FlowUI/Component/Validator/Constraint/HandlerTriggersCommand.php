<?php

namespace FlowUI\Component\Validator\Constraint;

use FlowUI\Component\Validator\Constraint;
use FlowUI\Component\Validator\Violation;
use FlowUI\Component\Network\Node\Command;
use FlowUI\Component\Network\Node\Handler;
use FlowUI\Component\Network\Node;

class HandlerTriggersCommand implements Constraint
{
    /**
     * {@inheritdoc
     */
    public function supportNode(Node $node)
    {
        return $node instanceof Handler;
    }

    /**
     * @param Handler $node
     * @return Violation[]
     */
    public function validate(Node $node)
    {
        $violations = [];
        foreach ($node->getMessages() as $message) {
            if ($message instanceof Command) {
                $violations[] = new Violation(
                    $node,
                    sprintf('Handler \'%s\' is triggering the command \'%s\'.', $node->getId(), $message->getId()),
                    Violation::ERROR
                );
            }
        }

        return $violations;
    }
}
 