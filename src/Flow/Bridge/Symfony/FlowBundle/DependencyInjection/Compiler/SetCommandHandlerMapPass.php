<?php

namespace Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SetCommandHandlerMapPass implements CompilerPassInterface
{
    private $tag;
    private $keyAttribute;

    /**
     * @param string  $tag
     * @param string  $keyAttribute
     */
    public function __construct($tag, $keyAttribute)
    {
        $this->tag = $tag;
        $this->keyAttribute = $keyAttribute;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $handlers = array();

        foreach ($container->findTaggedServiceIds($this->tag) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                if (!isset($tagAttributes[$this->keyAttribute])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'The attribute "%s" of tag "%s" of service "%s" is mandatory',
                            $this->keyAttribute,
                            $this->tag,
                            $serviceId
                        )
                    );
                }

                $key = $tagAttributes[$this->keyAttribute];

                $handlers[ltrim($key, '\\')] = [
                    'id' => $serviceId,
                    'class' => $container->findDefinition($serviceId)->getClass()
                ];
            }
        }

        $container->setParameter('flow.map.command_handler', $handlers);
    }
}
