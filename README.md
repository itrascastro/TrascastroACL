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

- Copy the 'roles.global.dist' from TrascastroACL config directory and paste it to config/autoload folder removing the
'.dist' termination. Now add your application roles:

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
