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

namespace TrascastroACL\View\Helper\Factory;


use TrascastroACL\View\Helper\AclHelper;

class AclHelperFactory
{
    public function __invoke($serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $acl = $sm->get('TrascastroACL');

        return new AclHelper($acl);
    }
}