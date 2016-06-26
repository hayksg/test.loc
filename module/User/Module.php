<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as AuthAdapter;

use User\Form\RegisterFilter;
use User\Form\RegisterForm;
use User\Form\LoginFilter;
use User\Form\LoginForm;
use User\Form\UserEditFilter;
use User\Form\UserEditForm;
use User\Form\UploadAddFilter;
use User\Form\UploadAddForm;
use User\Form\UploadEditFilter;
use User\Form\UploadEditForm;
use User\Form\UploadAddSharingFilter;
use User\Form\UploadAddSharingForm;
use User\Model\User;
use User\Model\UserTable;
use User\Model\Upload;
use User\Model\UploadTable;

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
                // Table user
                'UserTable' => function($sm)
                {
                    $tableGateway = $sm->get('UserTableGateway');
                    $userTable = new UserTable($tableGateway);
                    return $userTable;
                },
                'UserTableGateway' => function($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new Resultset();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                
                // Table upload
                'UploadTable' => function($sm)
                {
                    $tableGateway = $sm->get('UploadTableGateway');
                    $uploadSharingTableGateway = $sm->get('UploadSharingTableGateway');
                    $uploadTable = new UploadTable($tableGateway, $uploadSharingTableGateway);
                    return $uploadTable;  
                },
                'UploadTableGateway' => function($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Upload());
                    return new TableGateway('upload', $dbAdapter, null, $resultSetPrototype);
                },
                'UploadSharingTableGateway' => function($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');                    
                    return new TableGateway('upload_sharing', $dbAdapter);
                },
                
                // Table chat_messages
                'ChatMessagesTableGateway' => function($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new TableGateway('chat_messages', $dbAdapter);
                },
                
                // Forms
                'RegisterForm' => function($sm)
                {
                    $form = new RegisterForm();
                    $form->setInputFilter($sm->get('RegisterFilter'));
                    return $form;
                },
                'LoginForm' => function($sm)
                {
                    $form = new LoginForm();
                    $form->setInputFilter($sm->get('LoginFilter'));
                    return $form;
                },
                'UserEditForm' => function($sm)
                {
                    $form = new UserEditForm();
                    $form->setInputFilter($sm->get('UserEditFilter'));
                    return $form;
                },
                'UploadAddForm' => function($sm)
                {
                    $form = new UploadAddForm();
                    $form->setInputFilter($sm->get('UploadAddFilter'));
                    return $form;
                },
                'UploadEditForm' => function($sm)
                {
                    $form = new UploadEditForm();
                    $form->setInputFilter($sm->get('UploadEditFilter'));
                    return $form;
                },
                'UploadAddSharingForm' => function($sm)
                {
                    $form = new UploadAddSharingForm();
                    $form->setInputFilter($sm->get('UploadAddSharingFilter'));
                    return $form;
                },
                
                // Filters
                'RegisterFilter' => function($sm)
                {
                    return new RegisterFilter();
                },
                'LoginFilter' => function($sm)
                {
                    return new LoginFilter();
                },
                'UserEditFilter' => function($sm)
                {
                    return new UserEditFilter();
                },
                'UploadAddFilter' => function($sm)
                {
                    return new UploadAddFilter();
                },
                'UploadEditFilter' => function($sm)
                {
                    return new UploadEditFilter();
                },
                'UploadAddSharingFilter' => function($sm)
                {
                    return new UploadAddSharingFilter();
                },
                
                // AuthService
                'AuthService' => function($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $authAdapter = new AuthAdapter($dbAdapter, 'user', 'email', 'password', 'md5(?)');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($authAdapter);
                    return $authService;
                },
                'LoggedInUser' => function ($sm)
                {                   
                    $authService = $sm->get('AuthService');
                    $email = $authService->getStorage()->read();
                
                    $userTable = $sm->get('UserTable');
                    $user = $userTable->getUserByColumn(array('email' => $email));
                    return $user;
                }
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
        
        $sharedEventManager = $eventManager->getSharedManager(); // Общий менеджер событий
        $sharedEventManager->attach(
            __NAMESPACE__, MvcEvent::EVENT_DISPATCH, function ($e) {
                $controller = $e->getTarget(); // обслуживаемый контроллер
                $controllerName = $controller->getEvent()->getRouteMatch()->getParam('controller');               
                $controllersArray = array('User\Controller\Register',
                                          'User\Controller\Login',
                                          'User\Controller\ManageUser',
                                          'User\Controller\ManageUpload',
                                          'User\Controller\GroupChat');
                if (in_array($controllerName, $controllersArray)) {
                    $controller->layout('layout/user');
                }
            }
        );
    }
}
