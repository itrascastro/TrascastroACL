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

namespace TrascastroACL\Handler;


use TrascastroACL\Provider\RoleProviderInterface;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\AclInterface;

class RouteHandler
{
    /**
     * @var AclInterface
     */
    private $acl;

    /**
     * @var array
     */
    private $config;

    /**
     * @var RoleProviderInterface
     */
    private $roleProvider;

    /**
     * @param AclInterface $acl
     * @param array $config
     * @param RoleProviderInterface $roleProvider
     */
    function __construct(AclInterface $acl, $config, RoleProviderInterface $roleProvider)
    {
        $this->acl = $acl;
        $this->config = $config;
        $this->roleProvider = $roleProvider;
    }

    /**
     * handler
     *
     * Handles MvcEvent::EVENT_ROUTE
     *
     * @param MvcEvent $event
     */
    public function handler(MvcEvent $event)
    {
        $match = $event->getRouteMatch();

        if (!$match) { // we need a route
            return;
        }

        $role = $this->roleProvider->getUserRole();

        if (!$this->acl->isAllowed($role, $match->getMatchedRouteName())) {
            $controller = $this->config['TrascastroACL']['forbidden']['controller'];
            $action     = $this->config['TrascastroACL']['forbidden']['action'];
            $response   = $event->getResponse();

            $response->setStatusCode(Response::STATUS_CODE_403); // Forbidden
            $match->setParam('controller', $controller);
            $match->setParam('action', $action);
        }

        // we make the acl available on views
        $event->getViewModel()->setVariable('acl', $this->acl);
    }
}
