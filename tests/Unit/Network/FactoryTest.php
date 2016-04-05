<?php

namespace EJM\Flow\Tests\Unit\Network;

use EJM\Flow\Network\Factory;
use EJM\Flow\Network\Factory\AssemblyStage;
use EJM\Flow\Network\Network;
use PHPUnit_Framework_TestCase;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $stage1 = $this->getAssemblyStageMock();
        $stage2 = $this->getAssemblyStageMock();

        $factory = new Factory();
        $factory->addAssemblyStage($stage1);
        $factory->addAssemblyStage($stage2);

        $network = $factory->create();

        $this->assertEquals(new Network([]), $network);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAssemblyStageMock()
    {
        $stage = $this->getMock('\EJM\Flow\Network\Factory\AssemblyStage');

        $stage->expects($this->once())
            ->method('assemble')
            ->with($this->isInstanceOf('\EJM\Flow\Network\Blueprint'));

        return $stage;
    }
}
