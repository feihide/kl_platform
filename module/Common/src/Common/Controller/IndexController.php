<?php

namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class IndexController extends AbstractActionController
{		
    public function testAction()
	{		$request = $this->getRequest();
	
	        // Make sure that we are running in a console and the user has not tricked our
	        // application into running this action from a public web server.
	        if (!$request instanceof ConsoleRequest){
	            throw new \RuntimeException('You can only use this action from a console!');
	        }
	    	return $request->getParam('user');
	    	//print_r($request);
	    	return  'ok';
    }
    
   


}
