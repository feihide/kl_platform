<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */
namespace Portal;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $serviceManager = $app->getServiceManager();
        $serviceManager->setFactory('Portal\Event\Listener', function () use($serviceManager) { return new \Portal\Event\Listener($serviceManager); });
        $app->getEventManager()->attach($serviceManager->get('Portal\Event\Listener'));
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
