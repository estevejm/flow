<?php

namespace EJM\Flow\Network;

use EJM\Flow\Network\Node;

interface NetworkInterface
{
    /**
     * @return Node[]
     */
    public function getNodes();
}
