<?php

namespace Flow\Network\Factory;

use Flow\Network\Blueprint;

/**
 * Todo: rename to AssemblyStep
 */
interface Step
{
    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint);
}
