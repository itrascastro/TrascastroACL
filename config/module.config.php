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

return array(
    'service_manager' => array(
        'factories' => array(
            'TrascastroACL' => 'TrascastroACL\Service\Factory\AclServiceFactory',
            'TrascastroACL\Handler\RouteHandler'  => 'TrascastroACL\Handler\Factory\RouteHandlerFactory',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'acl' => 'TrascastroACL\View\Helper\Factory\AclHelperFactory',
        ),
    ),
);
