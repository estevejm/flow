<?php

namespace FlowUI\Component\Validator;

use FlowUI\FlowBundle\Model\Node;

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
     * @param Node[] $nodes
     * @return Violation[]
     */
    public function validate(array $nodes)
    {
        $violations = [];
        foreach ($nodes as $node) {
            foreach ($this->constraints as $constraint) {
                if ($constraint->supportNode($node)) {
                    $violations = array_merge($violations, $constraint->validate($node));
                }
            }
        }

        return $violations;
    }
}
 