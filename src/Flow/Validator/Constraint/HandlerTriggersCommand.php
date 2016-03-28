<?php

namespace Flow\Validator\Constraint;

use Flow\Validator\Constraint;
use Flow\Validator\Violation;
use Flow\Network\Node\Command;
use Flow\Network\Node\Handler;
use Flow\Network\Node;

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
 