<?php

namespace Flow\Validator;

use Assert\Assertion;
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
        Assertion::allIsInstanceOf($constraints, Constraint::class);

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
