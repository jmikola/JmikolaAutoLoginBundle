<?php

namespace Jmikola\AutoLoginBundle\DependencyInjection\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutoLoginFactory implements SecurityFactoryInterface
{
    /**
     * @see Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface::create()
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'jmikola_auto_login.security.authentication.provider.'.$id;
        $provider = $container
            ->setDefinition($providerId, new DefinitionDecorator('jmikola_auto_login.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
            ->replaceArgument(2, $id)
        ;

        if ($config['auto_login_user_provider']) {
            $provider->addArgument(new Reference($config['auto_login_user_provider']));
        }

        // Fall back to security.context service for BC with Symfony <2.6
        $tokenStorageReference = interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')
            ? new Reference('security.token_storage')
            : new Reference('security.context');

        $listenerId = 'jmikola_auto_login.security.authentication.listener.'.$id;
        $container
            ->setDefinition($listenerId, new DefinitionDecorator('jmikola_auto_login.security.authentication.listener'))
            ->replaceArgument(0, $tokenStorageReference)
            ->replaceArgument(2, $id)
            ->replaceArgument(3, $config['token_param'])
            ->replaceArgument(6, array(
                'override_already_authenticated' => $config['override_already_authenticated'],
            ))
        ;

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * @see Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface::getKey()
     */
    public function getKey()
    {
        return 'jmikola-auto-login';
    }

    /**
     * @see Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface::getPosition()
     */
    public function getPosition()
    {
        return 'remember_me';
    }

    /**
     * @see Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface::addConfiguration()
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $builder = $node->children();

        $builder
            ->scalarNode('auto_login_user_provider')->defaultNull()->end()
            ->scalarNode('provider')->end()
            ->scalarNode('token_param')->defaultValue('_al')->end()
            ->booleanNode('override_already_authenticated')->defaultFalse()->end()
        ;
    }
}
