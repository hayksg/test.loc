<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ContactUs;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use ContactUs\Form\ContactFilter;
use ContactUs\Form\ContactForm;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                
                // Form
                'ContactForm' => function($sm)
                {
                    $form = new ContactForm();
                    $form->setInputFilter($sm->get('ContactFilter'));
                    return $form;
                },
                
                // Filter
                'ContactFilter' => function($sm)
                {
                    return new ContactFilter();
                },
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach(
            __NAMESPACE__, MvcEvent::EVENT_DISPATCH, function ($e) {
                $controller = $e->getTarget();
                $controllerName = $controller->getEvent()->getRouteMatch()->getParam('controller');
                if (in_array($controllerName, array('ContactUs\Controller\Index'))) {
                    $controller->layout('layout/contact');
                }
            }    
        );
    }
}
