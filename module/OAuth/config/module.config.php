<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */

namespace OAuth;

return array(
    'router' => array(
        'routes' => array(
            'oauth' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/oauth',
                    'defaults' => array(
                        '__NAMESPACE__' => 'OAuth\Server\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'oauth_authorization' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/authorization',
                            'defaults' => array(
                                '__NAMESPACE__' => 'OAuth\Server\Controller',
                                'controller' => 'Authorization',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'oauth_token' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/token',
                            'defaults' => array(
                                '__NAMESPACE__' => 'OAuth\Server\Controller',
                                'controller' => 'Token',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'oauth_resource' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/resource',
                            'defaults' => array(
                                '__NAMESPACE__' => 'OAuth\Server\Controller',
                                'controller' => 'Resource',
                                'action' => 'index',    
                            ),    
                        ),    
                    ),
                ),    
            ),    
        ),    
    ),

    'controllers' => array(
        'invokables' => array(
            'OAuth\Server\Controller\Index' => 'OAuth\Server\Controller\IndexController',
            'OAuth\Server\Controller\Authorization' => 'OAuth\Server\Controller\AuthorizationController',
            'OAuth\Server\Controller\Token' => 'OAuth\Server\Controller\TokenController',
            'OAuth\Server\Controller\Resource' => 'OAuth\Server\Controller\ResourceController',
        ),
    ),

    'view_manager' => array(
        'doctype' => 'HTML5',
        'template_map' => array(
            'oauth/index/index' => __DIR__ . '/../view/oauth/index/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'oauth_server' => function($sm) {
                $storage = new Server\Storage\Db($sm->get('oauth_db'));
                $server = new \OAuth2\Server($storage);
                $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
                $server->addGrantType(new \OAuth2\GrantType\UserCredentials($storage));
                $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));

                return $server;
            },    
        ),
        'invokables' => array(
            'oauth_response' => '',    
        ),
    ),
);
