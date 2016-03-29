<?php

namespace Flow\Bridge\Symfony\FlowBundle;

use Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\AssemblyStagePass;
use Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\SetCommandHandlerMapPass;
use Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\SetEventSubscriberMapPass;
use Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\ValidatorConstraintPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FlowFlowBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SetCommandHandlerMapPass('command_handler', 'handles'));
        $container->addCompilerPass(new SetEventSubscriberMapPass('event_subscriber', 'subscribes_to'));
        $container->addCompilerPass(new ValidatorConstraintPass());
        $container->addCompilerPass(new AssemblyStagePass());
    }
}
