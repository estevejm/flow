<?php

namespace EJM\Flow\Bridge\Symfony\FlowBundle\Tests\DependencyInjection\Compiler;

use EJM\Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\ValidatorConstraintPass;
use PHPUnit_Framework_TestCase;

class ValidatorConstraintPassTest extends PHPUnit_Framework_TestCase
{
    const SERVICE_1_ID = 'service1';
    const SERVICE_2_ID = 'service2';

    /**
     * @var ValidatorConstraintPass
     */
    private $compilerPass;

    protected function setUp()
    {
        $this->compilerPass = new ValidatorConstraintPass();
    }

    public function testProcess()
    {
        $validatorService = $this->getDefinitionMock();
        $taggedService1 = $this->getDefinitionMock();
        $taggedService2 = $this->getDefinitionMock();

        $validatorService->expects($this->at(0))
            ->method('addMethodCall')
            ->with('addConstraint', [$taggedService1]);

        $validatorService->expects($this->at(1))
            ->method('addMethodCall')
            ->with('addConstraint', [$taggedService2]);

        $container = $this->getContainerBuilderMock();

        $container->expects($this->once())
            ->method('has')
            ->with(ValidatorConstraintPass::VALIDATOR_SERVICE_ID)
            ->willReturn(true);

        $container->expects($this->exactly(3))
            ->method('findDefinition')
            ->will($this->returnValueMap([
                [ValidatorConstraintPass::VALIDATOR_SERVICE_ID, $validatorService],
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

    public function testProcessWhenValidatorNotDefined()
    {
        $container = $this->getContainerBuilderMock();

        $container->expects($this->once())
            ->method('has')
            ->with(ValidatorConstraintPass::VALIDATOR_SERVICE_ID)
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
