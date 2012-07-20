<?php

namespace Jmikola\AutoLoginBundle\DependencyInjection\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutoLoginFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('auto_login_user_provider', null);
        $this->addOption('token_param', '_al');
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
     * {@inheritdoc}
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'jmikola_auto_login.security.authentication.provider.'.$id;
        $provider = $container
            ->setDefinition($providerId, new DefinitionDecorator('jmikola_auto_login.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id)
        ;

        if ($config['auto_login_user_provider']) {
            $provider->addArgument(new Reference($config['auto_login_user_provider']));
        }

        return $providerId;
    }

    /**
     * {@inheritdoc}
     */
    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = $this->getListenerId();
        $listener = new DefinitionDecorator($listenerId);
        $listener->replaceArgument(2, $id);
        $listener->replaceArgument(3, $config['token_param']);

        $listenerId .= '.'.$id;
        $container->setDefinition($listenerId, $listener);

        return $listenerId;
    }

    /**
     * {@inheritdoc}
     */
    protected function getListenerId()
    {
        return 'jmikola_auto_login.security.authentication.listener';
    }
}
