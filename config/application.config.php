<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id$
 */
return array(
    'modules' => array(
        'CL',
        'OAuth', 
        //'ZendDeveloperTools',
       // 'DoctrineModule',
      //  'DoctrineORMModule',
		'Platform',
    	'Common',
    	'Portal',
        'Ticket',
        'Passport',
//        'Test',
    ),

    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),

        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
    ),
	
		
);
