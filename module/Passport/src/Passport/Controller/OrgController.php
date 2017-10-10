<?php
namespace Passport\Controller;

use CL\Controller\PassportBaseController;

class OrgController extends PassportBaseController
{
	#注册机构管理员
	#author ym
	public function create($data)
	{

		if(empty($data)) {
			return $this->renderJson(array('message'=>'fail'));
		}
		$data['type'] = '3';

                $result = array();
                $status = 1;
		if($passport_id = $this->getDao('passport' , 'passport')->addPassport($data)) {
                    $result['passport_id'] = $passport_id;
                    $status = 0;
		}
		return $this->renderJson($result, $status);
	}
}
