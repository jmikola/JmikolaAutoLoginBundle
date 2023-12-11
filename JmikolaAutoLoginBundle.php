<?php

namespace Jmikola\AutoLoginBundle;

use Jmikola\AutoLoginBundle\DependencyInjection\Security\AutoLoginFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

@trigger_deprecation('jmikola/autologin', '2.0.2', 'The "%s" class is deprecated, use Symfony Components with AccessTokenAuthenticator instead. (see README.md)', JmikolaAutoLoginBundle::class);
class JmikolaAutoLoginBundle extends Bundle
{
    /**
     * @see Symfony\Component\HttpKernel\Bundle\Bundle::build()
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new AutoLoginFactory());
    }
}
