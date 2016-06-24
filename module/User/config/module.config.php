<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\Login'        => 'User\Controller\LoginController',
            'User\Controller\Register'     => 'User\Controller\RegisterController',
            'User\Controller\ManageUser'   => 'User\Controller\ManageUserController',
            'User\Controller\ManageUpload' => 'User\Controller\ManageUploadController',
            'User\Controller\GroupChat'    => 'User\Controller\GroupChatController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'user' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/user',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'User\Controller',
                        'controller'    => 'Login',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    'manage' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/manage[/page/:page][/:action][/:id]',
                            'constraints' => array(                                
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                                'page'   => '[0-9]+',
                            ),
                            'defaults' => array(                      
                                '__NAMESPACE__' => 'User\Controller',
                                'controller'    => 'ManageUser',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'upload' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/upload[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',                              
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'User\Controller',
                                'controller'    => 'ManageUpload',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'chat' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/chat',                          
                            'defaults' => array(
                                '__NAMESPACE__' => 'User\Controller',
                                'controller'    => 'GroupChat',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'user' => __DIR__ . '/../view',
        ),
    ),
    'module_config' => array(
        'upload_location' => __DIR__. '/../data/uploads',
    ),
);
