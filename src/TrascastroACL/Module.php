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

namespace TrascastroACL;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'routeHandler'), -100);
    }

    public function routeHandler(MvcEvent $event)
    {
        $match = $event->getRouteMatch();

        if (!$match) { // we need a route
            return;
        }

        $sm = $event->getApplication()->getServiceManager();
        $authenticationService = $sm->get('Zend\Authentication\AuthenticationService');

        /**
         * @var Acl $acl
         */
        $acl = $sm->get('TrascastroACL');

        $role = ($identity = $authenticationService->getIdentity()) ? $identity->role : 'guest';

        if (!$acl->isAllowed($role, $match->getMatchedRouteName())) {
            $config         = $sm->get('config');
            $controller     = $config['TrascastroACL']['forbidden']['controller'];
            $action         = $config['TrascastroACL']['forbidden']['action'];
            $response       = $event->getResponse();

            $response->setStatusCode(401); // Auth required
            $match->setParam('controller', $controller);
            $match->setParam('action', $action);
        }

        $event->getViewModel()->setVariable('acl', $acl);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
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
