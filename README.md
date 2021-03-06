TrascastroACL
=============

This module creates an ACL service from your routes. Avoid handwriting ACL permissions rules for each role or route in your application.

Installation
------------

Installation of TrascastroACL uses composer. For composer documentation, please refer to getcomposer.org.

    php composer.phar require itrascastro/acl:dev-master


Configuration
-------------

- Add the module name 'TrascastroACL' to your config/application.config.php

```php
array(
    'modules' => array(
        'Application',
        'TrascastroACL',
    ),
);
```

- Copy the 'TrascastroACL.global.dist' from TrascastroACL config directory and paste it to config/autoload folder removing the '.dist' termination. Now add your application roles and also add the 'controller' and the 'action' where the ACL will redirect unallowed access tries. You also need to add a role provider:

```php
return [
    'TrascastroACL' => [
        'roles' => [
            'guest',
            'user',
            'admin',
        ],
        'forbidden' => [
            'controller' => 'YOUR_FORBIDDEN_MANAGER_CONTROLLER',
            'action'     => 'YOUR_FORBIDDEN_MANAGER_ACTION',
        ],
        'role_provider' => 'YOUR_ROLE_PROVIDER',
    ],
];
```

The role provider must implement the interface 'TrascastroACL\Provider\RoleProviderInterface':

```php
namespace TrascastroACL\Provider;

interface RoleProviderInterface 
{
    /**
     * @return String
     */
    public function getUserRole();
}
```

This is an example of a role provider class:

```php
namespace User\Provider;

use TrascastroACL\Provider\RoleProviderInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\AuthenticationService;

class RoleProvider implements RoleProviderInterface
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @param AuthenticationServiceInterface $authenticationService
     */
    public function __construct(AuthenticationServiceInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @return String
     */
    public function getUserRole()
    {
        return ($identity = $this->authenticationService->getIdentity()) ? $identity->role : 'guest';
    }
}
```

Where the Factory would be as follows:

```php
namespace User\Provider\Factory;

use User\Provider\RoleProvider;

class RoleProviderFactory
{
    public function __invoke($serviceLocator)
    {
        $authenticationService = $serviceLocator->get('User\Service\Authentication');

        return new RoleProvider($authenticationService);
    }
}
```

Do not forget to add your provider to your module.config.php:

```php
'service_manager' => array(
    'factories' => array(
        // [ ... ]
        'User\Provider\RoleProvider' => 'User\Provider\Factory\RoleProviderFactory',
    ),
),
```

Usage
-----

Now you can manage your application access control from your routes by simply adding a 'roles' key like in this example:

```php
array(
    'router' => array(
        'routes' => array(
            'user\users\update' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/admin/users/update/id/:id/',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User\Controller\Users',
                        'action'     => 'update',
                        'roles'      => ['admin', 'moderator'],
                    ),
                ),
            ),
        ),
    ),
);
```

Only users with 'admin' or 'moderator' roles can now access to that route. If you do not create the 'roles' key in a route or you left it empty, then the resource will be public.

Accessing the Acl Service
-------------------------

- **From a Controller**

```php
$acl = $this->serviceLocator->get('TrascastroACL');
```

- **onBootstrap**

```php
<?php

namespace MyModule;

use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $sm  = $e->getApplication()->getServiceManager();
        $acl = $sm->get('TrascastroACL');
    }

    ...
}
```

- **From Views**

This module provides a View Helper to have access to TrascastroACL in your views:

```php
<?php if ($this->tacl()->isAllowed($this->identity()->role, 'admin\users\update')): ?>
```

It is also available using the layout() View Helper:

```php
<?php if ($this->layout()->tacl->isAllowed($this->identity()->role, 'admin\users\update')): ?>
```

- **From Layout**

```php
<?php if ($this->tacl()->isAllowed($this->identity()->role, 'admin\users\update')): ?>
```

It is also available using a layout variable:

```php
<?php if ($this->tacl->isAllowed($this->identity()->role, 'admin\users\update')): ?>
```
