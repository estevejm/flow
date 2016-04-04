<?php

namespace EJM\Flow\Tests\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler;

use EJM\Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\SetEventSubscriberMapPass;
use PHPUnit_Framework_TestCase;

class SetEventSubscriberMapPassTest extends PHPUnit_Framework_TestCase
{
    const SERVICE_1_ID = 'service1';
    const SERVICE_2_ID = 'service2';
    const SERVICE_3_ID = 'service3';

    /**
     * @var SetEventSubscriberMapPass
     */
    private $compilerPass;

    protected function setUp()
    {
        $this->compilerPass = new SetEventSubscriberMapPass('tagged_service', 'key_attribute');
    }

    public function testProcess()
    {
        $expectedMap = [
            'key1' => [
                [
                    'id' => self::SERVICE_1_ID,
                    'class' => 'service1_class',
                ],
                [
                    'id' => self::SERVICE_3_ID,
                    'class' => 'service3_class',
                ],
            ],
            'key2' => [
                [
                    'id' => self::SERVICE_2_ID,
                    'class' => 'service2_class',
                ],
            ]
        ];

        $taggedService1 = $this->getDefinitionMock();

        $taggedService1->expects($this->once())
            ->method('getClass')
            ->willReturn('service1_class');

        $taggedService2 = $this->getDefinitionMock();

        $taggedService2->expects($this->once())
            ->method('getClass')
            ->willReturn('service2_class');

        $taggedService3 = $this->getDefinitionMock();

        $taggedService3->expects($this->once())
            ->method('getClass')
            ->willReturn('service3_class');

        $container = $this->getContainerBuilderMock();

        $container->expects($this->exactly(3))
            ->method('findDefinition')
            ->will($this->returnValueMap([
                [self::SERVICE_1_ID, $taggedService1],
                [self::SERVICE_2_ID, $taggedService2],
                [self::SERVICE_3_ID, $taggedService3],
            ]));

        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('tagged_service')
            ->willReturn([
                self::SERVICE_1_ID => ['tagged_service' => ['key_attribute' => 'key1']],
                self::SERVICE_2_ID => ['tagged_service' => ['key_attribute' => 'key2']],
                self::SERVICE_3_ID => ['tagged_service' => ['key_attribute' => 'key1']],
            ]);

        $container->expects($this->once())
            ->method('setParameter')
            ->with(SetEventSubscriberMapPass::PARAMETER_ID, $expectedMap);

        $this->compilerPass->process($container);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testProcessWithMissingTagAttributes()
    {
        $container = $this->getContainerBuilderMock();

        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('tagged_service')
            ->willReturn([
                self::SERVICE_1_ID => ['tagged_service' => []],
                self::SERVICE_2_ID => ['tagged_service' => []],
            ]);

        $container->expects($this->never())
            ->method('setParameter');

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
