# JmikolaAutoLoginBundle

This bundle integrates the [AutoLogin][] library with Symfony, which implements
a security firewall listener to authenticate users based on a single query
parameter. This is useful for providing one-click login functionality in email
and newsletter links.

  [AutoLogin]: https://github.com/jmikola/AutoLogin

## Installation

The bundle is published as a [package][] and is installable via [Composer][]:

```
$ composer require jmikola/auto-login-bundle=~1.0
```

Activate the bundle in your application kernel:

```php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Jmikola\AutoLoginBundle\JmikolaAutoLoginBundle(),
            // ...
        );
    }
}
```

  [package]: https://packagist.org/packages/jmikola/auto-login-bundle
  [Composer]: http://getcomposer.org/

### Compatibility

This bundle requires Symfony 2.2 or above.

* 1.0 - Support for Symfony : `2.2 - 4.3`.
* 2.0 - Support for Symfony: `4.3+`.

## Usage and Configuration

You need to create a new `UserProvider` service that implements
`Jmikola\AutoLogin\User\AutoLoginUserProviderInterface`. This service is
responsible for resolving the URL token to a user object.

This bundle registers a firewall listener, which is configured via the
`jmikola_auto_login` key in your security component's firewall configuration.
See this example configuration:

```yml
# services.yml
services:
  acme.auto_login_user_provider:
    # Implements Jmikola\AutoLogin\User\AutoLoginUserProviderInterface
    class: Acme\UserBundle\Security\AcmeAutoLoginUserProvider
```

```yml
# security.yml
security:
  firewalls:
    main:
      # We need not specify a "provider" for our firewall or listeners,
      # since SecurityBundle will default to the first provider defined.
      jmikola_auto_login:
        auto_login_user_provider: acme.auto_login_user_provider
```

In the example above, we specify a custom service for `auto_login_user_provider`
since the default `EntityUserProvider` does not implement
`AutoLoginUserProviderInterface`.

When visiting `http://example.com/path?_al=foobar`,
`AcmeAutoLoginUserProvider::loadUserByAutoLoginToken()` will be invoked with the
value "foobar". This method should resolve that value to a user object or throw
a `Jmikola\AutoLogin\Exception\AutoLoginTokenNotFoundException`.

### Listener Options

The AutoLoginFactory defines the following listener options:

 * `auto_login_user_provider`: AutoLoginUserProviderInterface service, which
    provides a method to load users by an auto-login token (i.e. query
    parameter). If this service is not defined, the listener's user provider
    will be used by default and an exception will be thrown if the provider does
    not implement the required interface (in addition to UserProviderInterface).
 * `provider`: User provider key. This is a standard option for most security
    listeners. If undefined, the default user provider for the firewall
    is used (see: [SecurityBundle documentation][]).
 * `token_param`: The query parameter to be checked for an auto-login token.
    The presence of this query parameter will determine if the auto-login
    listener attempts authentication. In that respect, it is similar to the
    `check_path` option for the form-login listener. If undefined, the option
    defaults to `_al`.
 * `override_already_authenticated`: Boolean option determines whether an
    auto-login token should override an existing, authenticated session. This
    option defaults to false.

  [SecurityBundle documentation]: http://symfony.com/doc/current/book/security.html#using-multiple-user-providers

### Alternative Configuration Example

In this example, we specify a provider that implements both
`Symfony\Component\Security\Core\User\UserProviderInterface` and
`Jmikola\AutoLogin\User\AutoLoginUserProviderInterface`. We also customize the
URL parameter to use "auto_login" instead of the default "_al":

```yml
# services.yml
services:
  acme.versatile_user_provider:
    # This class implements UserProviderInterface and
    # AutoLoginUserProviderInterface
    class: Acme\UserBundle\Security\VersatileUserProvider
```

```yml
# security.yml
security:
  providers:
    acme_user_provider:
      id: acme.versatile_user_provider
  firewalls:
    main:
      jmikola_auto_login:
        token_param: auto_login
        # We need not configure the auto_login_user_provider option here, as the
        # bundle will default to the firewall's default user provider, which
        # implements the necessary interface.
```

### FOSUserBundle Configuration Example

If you are using [FOSUserBundle][], defining a service ID for your user provider
will look familiar. You can easily integrate this bundle with FOSUserBundle by
defining a custom service for `fos_user.user_manager`:

```yml
services:
    acme.user_manager:
        # This class extends the appropriate UserManager from FOSUserBundle
        # and implements Jmikola\AutoLogin\User\AutoLoginUserProviderInterface
        class: Acme\UserBundle\Model\UserManager
        # Note: the remaining service configuration is abridged

fos_user:
    service:
        user_manager: acme.user_manager
```

  [FOSUserBundle]: https://github.com/FriendsOfSymfony/FOSUserBundle
