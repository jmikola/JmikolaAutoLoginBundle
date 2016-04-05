<?php

namespace Jmikola\AutoLoginBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    protected static function createKernel(array $options = array())
    {
        return new AppKernel(isset($options['config']) ? $options['config'] : 'default.yml');
    }
}