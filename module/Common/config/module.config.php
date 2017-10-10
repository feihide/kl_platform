<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */
return array(
		'console' => array(
				'router' => array(
						'routes' => array(
								'test' => array(
										'options' => array(
												'route'    => 'test [--verbose|-v] <user>',
												'defaults' => array(
														'controller' => 'Common\Controller\Index',
														'action'     => 'test'
												)
										)
								)
						)
				)
		),
		
	 'controllers' => array(
        'invokables' => array(
            'common\controller\index' => 'Common\Controller\IndexController',

        ),
    ),
);
