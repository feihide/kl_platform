<?php
namespace Passport\Controller;

use CL\Controller\PassportBaseController;

class PassportController extends PassportBaseController
{
	#登入
	public function get($id)
	{
		$name   = $_GET['name'];
		$pwd    = $_GET['pwd'];
		if($data = $this->getDao('passport' , 'passport')->login($name , $pwd )) {
			return $this->renderJson($data);
		}
		return $this->renderJson();
	}



}