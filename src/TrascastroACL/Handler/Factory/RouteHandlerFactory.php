<?php
/**
 * (c) Ismael Trascastro <i.trascastro@gmail.com>
 *
 * @link        http://www.ismaeltrascastro.com
 * @copyright   Copyright (c) Ismael Trascastro. (http://www.ismaeltrascastro.com)
 * @license     MIT License - http://en.wikipedia.org/wiki/MIT_License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TrascastroACL\Handler\Factory;


use TrascastroACL\Handler\RouteHandler;

class RouteHandlerFactory
{
    public function __invoke($serviceLocator)
    {
        $acl            = $serviceLocator->get('TrascastroACL');
        $config         = $serviceLocator->get('config');
        $roleProvider   = $serviceLocator->get($config['TrascastroACL']['role_provider']);

        return new RouteHandler($acl, $config, $roleProvider);
    }
}
