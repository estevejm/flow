<?php

namespace EJM\Flow\Network;

use EJM\Flow\Network\Builder\AssemblyStage;

class Builder
{
    /**
     * @var AssemblyStage[]
     */
    private $stages = [];

    /**
     * @param AssemblyStage $stage
     */
    public function addAssemblyStage(AssemblyStage $stage)
    {
        $this->stages[] = $stage;
    }

    /**
     * @return Network
     */
    public function create()
    {
        $blueprint = new Blueprint();

        foreach ($this->stages as $stage) {
            $stage->assemble($blueprint);
        }

        return new Network($blueprint->getNodes());
    }
}
