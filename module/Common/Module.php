<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */
namespace Common;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
class Module implements
    ConfigProviderInterface,
    ConsoleUsageProviderInterface  
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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
    
    public function getConsoleUsage(Console $console){
    	return array(
    			// Describe available commands
    			'test [--verbose|-v] user'    => 'Reset password for a user',
    
    			// Describe expected parameters
    			array( 'user',            'Email of the user for a password reset' ),
    			array( '--verbose|-v',     '(optional) turn on verbose mode'),
    	);
    }
    
}
