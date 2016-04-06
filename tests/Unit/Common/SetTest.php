<?php

namespace EJM\Flow\Tests\Unit\Common;

use EJM\Flow\Common\Set;
use PHPUnit_Framework_TestCase;

class SetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \EJM\Flow\Common\ElementNotFoundException
     */
    public function testGetWithUnexistingKey()
    {
        $set = new Set();
        $set->get('will not be found');
    }
}
