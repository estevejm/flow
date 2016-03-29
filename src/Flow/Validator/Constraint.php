<?php

namespace Flow\Validator;

use Flow\Network\Node;

interface Constraint
{
    /**
     * @param Node $node
     * @return boolean
     */
    public function supportsNode(Node $node);

    /**
     * @param Node $node
     * @return Violation[]
     */
    public function validate(Node $node);
}
