<?php

namespace EJM\Flow\Validator\Constraint;

use EJM\Flow\Validator\Constraint;
use EJM\Flow\Validator\Violation;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Handler;
use EJM\Flow\Network\Node;

class HandlerTriggersCommand implements Constraint
{
    /**
     * {@inheritdoc
     */
    public function supportsNode(Node $node)
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
 