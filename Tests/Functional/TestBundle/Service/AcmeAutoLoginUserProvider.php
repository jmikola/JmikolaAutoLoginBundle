<?php

namespace Jmikola\AutoLoginBundle\Tests\Functional\TestBundle\Service;

use Jmikola\AutoLogin\User\AutoLoginTokenNotFoundException;
use Jmikola\AutoLogin\User\AutoLoginUserProviderInterface;

class AcmeAutoLoginUserProvider implements AutoLoginUserProviderInterface
{
    public function loadUserByAutoLoginToken($key)
    {
        throw new AutoLoginTokenNotFoundException();
    }
}
