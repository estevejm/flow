<?php

namespace EJM\Flow\Network\Builder;

use EJM\Flow\Network\Blueprint;

interface AssemblyStage
{
    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint);
}
