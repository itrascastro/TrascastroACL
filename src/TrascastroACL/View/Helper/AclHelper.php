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

namespace TrascastroACL\View\Helper;


use Zend\Permissions\Acl\Acl;
use Zend\View\Helper\AbstractHelper;

class AclHelper extends AbstractHelper
{
    /**
     * @var Acl
     */
    private $acl;

    /**
     * @param Acl $acl
     */
    public function __construct(Acl $acl)
    {
        $this->acl = $acl;
    }

    public function __invoke()
    {
        return $this->acl;
    }
}
