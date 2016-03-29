<?php

namespace EJM\Flow\Bridge\Symfony\FlowBundle;

use EJM\Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\AssemblyStagePass;
use EJM\Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\SetCommandHandlerMapPass;
use EJM\Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\SetEventSubscriberMapPass;
use EJM\Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\ValidatorConstraintPass;
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
