<?php

namespace EJM\Flow\Tests\Unit\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler;

use EJM\Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\AssemblyStagePass;
use PHPUnit_Framework_TestCase;

class AssemblyStagePassTest extends PHPUnit_Framework_TestCase
{
    const SERVICE_1_ID = 'service1';
    const SERVICE_2_ID = 'service2';

    /**
     * @var AssemblyStagePass
     */
    private $compilerPass;

    protected function setUp()
    {
        $this->compilerPass = new AssemblyStagePass();
    }

    public function testProcess()
    {
        $factoryService = $this->getDefinitionMock();
        $taggedService1 = $this->getDefinitionMock();
        $taggedService2 = $this->getDefinitionMock();

        $factoryService->expects($this->at(0))
            ->method('addMethodCall')
            ->with('addAssemblyStage', [$taggedService1]);

        $factoryService->expects($this->at(1))
            ->method('addMethodCall')
            ->with('addAssemblyStage', [$taggedService2]);

        $container = $this->getContainerBuilderMock();

        $container->expects($this->once())
            ->method('has')
            ->with(AssemblyStagePass::FACTORY_SERVICE_ID)
            ->willReturn(true);

        $container->expects($this->exactly(3))
            ->method('findDefinition')
            ->will($this->returnValueMap([
                [AssemblyStagePass::FACTORY_SERVICE_ID, $factoryService],
                [self::SERVICE_1_ID, $taggedService1],
                [self::SERVICE_2_ID, $taggedService2],
            ]));

        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn([
                self::SERVICE_1_ID => [],
                self::SERVICE_2_ID => []
            ]);

        $this->compilerPass->process($container);
    }

    public function testProcessWhenFactoryNotDefined()
    {
        $container = $this->getContainerBuilderMock();

        $container->expects($this->once())
            ->method('has')
            ->with(AssemblyStagePass::FACTORY_SERVICE_ID)
            ->willReturn(false);

        $container->expects($this->never())
            ->method('findDefinition');

        $this->compilerPass->process($container);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContainerBuilderMock()
    {
        return $this->getMock('\Symfony\Component\DependencyInjection\ContainerBuilder');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDefinitionMock()
    {
        return $this->getMock('\Symfony\Component\DependencyInjection\Definition');
    }
}
