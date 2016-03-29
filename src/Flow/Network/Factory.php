<?php

namespace Flow\Network;

use Flow\Network\Factory\Step;

class Factory
{
    /**
     * @var Step[]
     */
    private $steps = [];

    /**
     * @param Step $step
     */
    public function addStep(Step $step)
    {
        $this->steps[] = $step;
    }

    /**
     * @return Network
     */
    public function create()
    {
        $blueprint = new Blueprint();

        foreach ($this->steps as $step) {
            $step->assemble($blueprint);
        }

        return new Network($blueprint->getNodes());
    }
}
