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

- Copy the 'TrascastroACL.global.dist' from TrascastroACL config directory and paste it to config/autoload folder removing the '.dist' termination. Now add your application roles ('guest' role is mandatory) and 
also add the 'controller' and the 'action' where the ACL will redirect unallowed access tries:

```php
return [
    'TrascastroACL' => [
        'roles' => [
            'guest',
            'user',
            'admin',
        ],
        'forbidden' => [
            'controller'    => 'YOUR_FORBIDDEN_MANAGER_CONTROLLER',
            'action'        => 'YOUR_FORBIDDEN_MANAGER_ACTION',
        ],
    ],
];
```

- Create an alias to your Authentication Service in your module.config.php:

```php
'service_manager' => array(
    'aliases' => array(
        'Zend\Authentication\AuthenticationService' => 'YOUR_AUTHENTICATION_SERVICE',
    ),
    'factories' => array(
        'YOUR_AUTHENTICATION_SERVICE' => 'MyModule\Service\Factory\AuthenticationServiceFactory',
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

- From a Controller

```php
$acl = $this->serviceLocator->get('TrascastroACL');
```

- onBootstrap

```php
<?php

namespace MyModule;

use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $acl = $sm->get('TrascastroACL');
    }

    ...
}
```

- From Views

You can ask for permissions in your views using the layout() View Helper as follows:

```php
<?php if ($this->layout()->acl->isAllowed($this->identity()->role, 'admin\users\update')): ?>
```
