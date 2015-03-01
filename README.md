TrascastroACL
=============

This module creates an ACL service from your routes.

Installation
------------

Installation of TrascastroACL uses composer. For composer documentation, please refer to getcomposer.org.

    php composer.phar require itrascastro/acl:dev-master


Usage
-----

- Add the module name 'TrascastroACL' to your config/application.config.php

```php
array(
    'modules' => array(
        'Application',
        'TrascastroACL',
    ),
);
```

- Copy the 'roles.global.dist' from TrascastroACL config directory and paste it to config/autoload folder removing the '.dist' termination. Now add your application roles:

```php
return [
    'application' => [
        'roles' => [
            'guest',
            'user',
            'admin',
        ],
    ],
];
```

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

````php
$acl = $this->serviceLocator->get('TrascastroACL');
````

- onBootstrap

````php
<?php
/**
 * (c) Ismael Trascastro <i.trascastro@gmail.com>
 *
 * @link        https://github.com/itrascastro/TrascastroACL
 * @copyright   Copyright (c) Ismael Trascastro. (http://www.ismaeltrascastro.com)
 * @license     MIT License - http://en.wikipedia.org/wiki/MIT_License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace User;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;

class Module implements AutoloaderProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'routeHandler'), -100);
    }

    public function routeHandler(MvcEvent $event)
    {
        $match = $event->getRouteMatch();

        if (!$match) { // we need a route
            return;
        }

        $sm = $event->getApplication()->getServiceManager();
        $authenticationService = $sm->get('User\Service\Authentication');

        /**
         * @var Acl $acl
         */
        $acl = $sm->get('TrascastroACL');

        $role = ($identity = $authenticationService->getIdentity()) ? $identity->role : 'guest';

        if (!$acl->isAllowed($role, $match->getMatchedRouteName())) {
            $response = $event->getResponse();
            $response->setStatusCode(401); // Auth required
            $match->setParam('controller', 'User\Controller\Users');
            $match->setParam('action', 'forbidden');
        }

        $event->getViewModel()->setVariable('acl', $acl);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
```