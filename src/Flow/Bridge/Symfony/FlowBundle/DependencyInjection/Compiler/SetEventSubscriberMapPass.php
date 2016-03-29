<?php

namespace Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SetEventSubscriberMapPass implements CompilerPassInterface
{
    private $tag;
    private $keyAttribute;

    /**
     * @param string  $tag                  The tag name of message subscriber services
     * @param string  $keyAttribute         The name of the tag attribute that contains the name of the subscriber
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

                $handlers[ltrim($key, '\\')][] = [
                    'id' => $serviceId,
                    'class' => $container->findDefinition($serviceId)->getClass()
                ];
            }
        }

        $container->setParameter('flow.map.events_subscribers', $handlers);
    }
}
