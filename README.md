TrascastroACL
=============

This module creates an ACL service from your routes.

Installation
------------

Add this require to your composer.json:

```json
    "require": {
            "itrascastro/trascastro-acl": "1.0.*@dev"
        }
```

Then update composer:

- If you have composer globally installed:

        composer update

- If not

        php composer.phar update

Installation 2
--------------

Run this:

    php composer.phar require "itrascastro/trascastro-acl"

When it request you for:

    Please provide a version constraint for the itrascastro/trascastro-acl requirement:

Introduce:

    *@dev

Usage
-----

- Add the module name 'TrascastroACL' to your application.config.php

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

Only users with 'admin' or 'moderator' roles can now access to that route. If you do not create the 'roles' key in a
route or you left it empty, then the resource will be public.
