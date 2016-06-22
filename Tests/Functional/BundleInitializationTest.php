<?php

namespace Jmikola\AutoLoginBundle\Tests\Functional;

class BundleInitializationTest extends BaseTestCase
{
    /**
     * @test
     */
    public function bundle_will_install_with_no_errors()
    {
        static::createClient();
    }
}