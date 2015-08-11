<?php

namespace Jmikola\AutoLoginBundle;

use Jmikola\AutoLoginBundle\DependencyInjection\Compiler\SecurityCompilerPass;
use Jmikola\AutoLoginBundle\DependencyInjection\Security\AutoLoginFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JmikolaAutoLoginBundle extends Bundle
{
    /**
     * @see Symfony\Component\HttpKernel\Bundle\Bundle::build()
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SecurityCompilerPass());

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new AutoLoginFactory());
    }
}
