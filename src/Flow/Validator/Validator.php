<?php

namespace Flow\Validator;

use Flow\Network\Network;
use Flow\Network\Node;

class Validator
{
    /**
     * @var Constraint[]
     */
    private $constraints;

    /**
     * @param Constraint[] $constraints
     */
    public function __construct(array $constraints)
    {
        // todo: assert array of constraints
        $this->constraints = $constraints;
    }

    /**
     * @param Network $network
     * @return Violation[]
     */
    public function validate(Network $network)
    {
        $violations = [];

        foreach ($network->getNodes() as $node) {
            foreach ($this->constraints as $constraint) {
                if ($constraint->supportNode($node)) {
                    $violations = array_merge($violations, $constraint->validate($node));
                }
            }
        }

        return new Validation($violations);
    }
}
