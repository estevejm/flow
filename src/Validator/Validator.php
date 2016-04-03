<?php

namespace EJM\Flow\Validator;

use EJM\Flow\Network\Network;
use EJM\Flow\Network\Node;

class Validator
{
    /**
     * @var Constraint[]
     */
    private $constraints = [];

    /**
     * @param Constraint $constraint
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->constraints[] = $constraint;
    }

    /**
     * @param Network $network
     * @return Validation
     */
    public function validate(Network $network)
    {
        $violations = [];

        foreach ($network->getNodes() as $node) {
            foreach ($this->constraints as $constraint) {
                if ($constraint->supportsNode($node)) {
                    $violations = array_merge($violations, $constraint->validate($node));
                }
            }
        }

        return new Validation($violations);
    }
}
