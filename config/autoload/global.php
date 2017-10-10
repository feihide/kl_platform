<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */
return array(

        'router' => array(
                'routes' => array(
                        'home' => array(
                                'type' => 'literal',
                                'options' => array(
                                        'route' => '/',
                                        'defaults' => array(
                                                'controller' => 'index',
                                                'action' => 'index',
                                                '__NAMESPACE__' => 'Platform\Controller',
                                        ),
                                ),
                        ),

                        'default' => array(
                                'type' => 'segment',
                                'options' => array(
                                        'route' => '/platform/:controller[/:action]',
                                        'constraints' => array(
                                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        ),
                                        'defaults' => array(
                                                '__NAMESPACE__' => 'Platform\Controller',
                                                'controller' => 'index',
                                                'action' => 'index',
                                        ),
                                ),
                        ),

                        'org_privilege' => array(
                                'type' => 'segment',
                                'options' => array(
                                        'route' => '/orgs/:org_id/:controller/:group_id/privileges[/[:id]]',
                                        'constraints' => array(
                                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                'id' => '[0-9_-]*',
                                                'org_id' => '[0-9_-]*',
                                                'group_id' => '[0-9_-]*',
                                        ),
                                        'defaults' => array(
                                                '__NAMESPACE__' => 'Org\Controller\privilege',
                                        ),
                                ),
                        ),

                        'org' => array(
                                'type' => 'segment',
                                'options' => array(
                                        'route' => '/orgs/:org_id/:controller[/[:id]]',
                                        'constraints' => array(
                                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                'id' => '[0-9_-]*',
                                                'org_id' => '[0-9_-]*',
                                        ),
                                        'defaults' => array(
                                                '__NAMESPACE__' => 'Org\Controller',
                                        ),
                                ),
                        ),
                        'passport' => array(
                                'type' => 'segment',
                                'options' => array(
                                        'route' => '/passports/:controller[/[:id]]',
                                        'constraints' => array(
                                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                'id' => '[a-zA-Z0-9_-]*',
                                        ),
                                        'defaults' => array(
                                                '__NAMESPACE__' => 'Passport\Controller',
                                        ),
                                ),
                        ),
                        'restful' => array(
                                'type' => 'segment',
                                'options' => array(
                                        'route' => '/:controller[/[:id]]',
                                        'constraints' => array(
                                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                'id' => '[0-9_-]*',
                                        ),
                                ),
                        ),

                ),

        ),

        'view_manager' => array(
                'strategies' => array(
                        'ViewJsonStrategy',
                ),
        ),

        'service_manager' => array(
                'factories' => array(
                        'dao_factory' => function ($sm) {
                            return new \CL\Dao\DaoFactory($sm);
                        },
                        // 返回数据库配置
                        'server_factory' => function ($sm) {
                            return new \CL\Dao\ServerFactory($sm);
                        },

                        'mongo' => function ($sm) {
                            return new \CL\Db\Mongodb(array(
                                    'host' => '192.168.1.163',
                                    'port' => 27017,
                            ));
                        },
                        'gearman' => function ($sm) {
                            $client = new \GearmanClient();
                            $client->addServer('192.168.1.182', 4730);

                            return $client;
                        },

                        // redis cache
                        'redis' => function ($sm) {
                            return new \CL\Cache\Rcache(array(
                                    'host' => '192.168.1.163',
                                    'port' => 6379,
                                    'timeout' => 3,
                                    'database' => 0,
                            ));
                        },

                        // emcache cache
                        'mcache' => function ($sm) {
                            return new \CL\Cache\Mcache(array(
                                    array(
                                            'host' => '192.168.1.221',
                                            'port' => 11211,
                                            'persistent' => false,
                                            'timeout' => 2,
                                            'weight' => 1,
                                    ),
                            ));
                        },
                        // sphinx
                        'sphinx' => function ($sm) {
                            return new \CL\Sphinx\Sphinx(array(
                                        'host' => '192.168.1.149',
                                        'port' => 9312,
                                        'timeout' => 2,
                            ));
                        },
                        'cl_config' => function ($sm) {
                            return array(
                                    'db' => array(
                                            'ticket_server' => array(
                                                    array(
                                                            'host' => '192.168.1.240',
                                                            'port' => 3306,
                                                            'database' => 'cl_ticket',
                                                            'username' => 'root',
                                                            'password' => '789789',
                                                            'weight' => 5,
                                                    ),
                                                    array(
                                                            'host' => '192.168.1.240',
                                                            'port' => 3306,
                                                            'database' => 'cl_ticket',
                                                            'username' => 'root',
                                                            'password' => '789789',
                                                            'weight' => 5,
                                                    ),
                                            ),

                                            'common_server' => array(
                                                    array(
                                                            'host' => '192.168.1.221',
                                                            'database' => 'mjb_platform',
                                                           'username' => 'mjbappadmin',
                                                           'password' => 'qDLTuXpEDx1VcChsne7n',
                                                    ),
                                            ),
                                                            'platform_server' => array(
                                            array(
                                                    'host' => '192.168.0.146', // '192.168.0.254',
                                                    'database' => 'kl_platform',
                                                     'username' => 'root',
                                                     'password' => 'redhat',
                                            ),
                                            ),
                                            'passport_server' => array(
                                                    array(
                                                            'host' => '192.168.1.240',
                                                            'database' => 'cl_passport',
                                                            'username' => 'root',
                                                            'password' => '789789',
                                                    ),
                                            ),

                                            'portal_server' => array(
                                                    'sh' => array(
                                                            array(
                                                                    'host' => '192.168.1.240',
                                                                    'database' => 'cl_portal_sh',
                                                                    'username' => 'cailang',
                                                                    'password' => '123456',
                                                            ),
                                                    ),
                                                    'hz' => array(
                                                            array(
                                                                    'host' => '192.168.1.240',
                                                                    'database' => 'cl_portal_hz',
                                                                    'username' => 'cailang',
                                                                    'password' => '123456',
                                                            ),
                                                    ),
                                            ),
                                    ),

                                    // 全局变量
                                    'params' => array(
                                            'phone_api_domain' => 'https://192.168.0.254',
                                            'api_domain' => '192.168.99.1:3004',
                                    ),
                            );
                        },
                ),

                'aliases' => array(
                        'ticket_db' => 'oauth_db',
                ),
        ),
);
