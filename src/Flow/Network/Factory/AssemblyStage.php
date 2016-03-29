<?php

namespace Flow\Network\Factory;

use Flow\Network\Blueprint;

interface AssemblyStage
{
    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint);
}
