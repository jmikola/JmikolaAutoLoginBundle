# JmikolaAutoLoginBundle

This bundle integrates the [AutoLogin][] library with Symfony2, which implements
a security firewall listener to authenticate users based on a single query
parameter. This is useful for providing one-click login functionality in email
and newsletter links.

  [AutoLogin]: https://github.com/jmikola/AutoLogin

## Installation

The bundle is published as a [package][] and is installable via [Composer][]:

```
$ composer require jmikola/auto-login-bundle=~1.0
```

You also need to activate the bundle in the AppKernel.php.

```php 
  
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            //b...
            new Jmikola\AutoLoginBundle\JmikolaAutoLoginBundle(),
            // ...
        );
    }
}
```
  [package]: https://packagist.org/packages/jmikola/auto-login-bundle
  [Composer]: http://getcomposer.org/

### Compatibility

This bundle requires Symfony 2.1 or above. There is no support for Symfony 2.0.

## Usage and configuration

You need to create a new `UserProvider` service that implements `Jmikola\AutoLogin\User\AutoLoginUserProviderInterface`. This 
service is responsable to fetch the correct user object from the URL token. 

This bundle registers a firewall listener, which is configured via the `jmikola_auto_login` key in your security 
component's firewall configuration. See this example configuration: 

```yml
// services.yml
services:
  acme.auto_login_user_provider:
    # Implements Jmikola\AutoLogin\User\AutoLoginUserProviderInterface
    class: Acme\UserBundle\Security\AcmeAutoLoginUserProvider
```
```yml
// security.yml
security:
  firewalls:
    main:
      # We need not specify a "provider" for our firewall or listeners,
      # since SecurityBundle will default to the first provider defined.
      jmikola_auto_login:
        auto_login_user_provider: acme.auto_login_user_provider
```

In the example above we need to specify a custom service for `auto_login_user_provider`, since default
`EntityUserProvider` does not implement `AutoLoginUserProviderInterface`.

When you go to the url `http://your-app.com/whatever?_al=foobar` we will invoke the `AcmeAutoLoginUserProvider::loadUserByAutoLoginToken` with parameter `foobar`. It is your job to make sure the `AcmeAutoLoginUserProvider` returns the correct user for that token. 

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

### An other security configuration example

In this example we specify a provider that implement both `UserProviderInterface` and `AutoLoginUserProviderInterface`. We do 
also set the URL token to listen for to `auto_login`.

```yml
services:
  acme.versatile_user_provider:
    # This class implements UserProviderInterface and
    # AutoLoginUserProviderInterface
    class: Acme\UserBundle\Security\VersatileUserProvider

security:
  providers:
    acme_user_provider:
      id: acme.versatile_user_provider
  firewalls:
    main:
      jmikola_auto_login:
        token_param: auto_login
        override_already_authenticated: false
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
