<?php

namespace Jmikola\AutoLoginBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SecurityCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        /**
         * Use \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage if it exists
         */
        if ($container->hasDefinition('security.token_storage')) {
            $container->setDefinition('jmikola_auto_login.token_storage_or_security_context', $container->getDefinition('security.token_storage'));
        } else {
            $container->setDefinition('jmikola_auto_login.token_storage_or_security_context', $container->getDefinition('security.context'));
        }
    }
}
