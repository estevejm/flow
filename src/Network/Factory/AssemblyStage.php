<?php

namespace EJM\Flow\Network\Factory;

use EJM\Flow\Network\Blueprint;

interface AssemblyStage
{
    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint);
}
