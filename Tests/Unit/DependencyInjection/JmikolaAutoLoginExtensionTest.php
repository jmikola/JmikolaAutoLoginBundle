<?php

namespace Jmikola\AutoLoginBundle\Tests\Unit\DependencyInjection;

use Jmikola\AutoLoginBundle\DependencyInjection\JmikolaAutoLoginExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class JmikolaAutoLoginExtensionTest extends AbstractExtensionTestCase
{
    public function testServicesRegisteredAfterLoading()
    {
        $this->load();

        $this->assertContainerBuilderHasService('jmikola_auto_login.security.authentication.provider', 'Jmikola\AutoLogin\Authentication\Provider\AutoLoginProvider');
        $this->assertContainerBuilderHasService('jmikola_auto_login.security.authentication.listener', 'Jmikola\AutoLogin\Http\Firewall\AutoLoginListener');
        $this->assertContainerBuilderHasServiceDefinitionWithTag('jmikola_auto_login.security.authentication.listener', 'monolog.logger', array('channel'=>'security'));
    }

    protected function getContainerExtensions(): array
    {
        return array(
            new JmikolaAutoLoginExtension(),
        );
    }
}
