<?php

namespace Flow\Network;

use Flow\Network\Node;

interface NetworkInterface
{
    /**
     * @return Node[]
     */
    public function getNodes();
}
