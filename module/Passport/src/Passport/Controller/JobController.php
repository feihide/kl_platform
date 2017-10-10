<?php
namespace Passport\Controller;

use CL\Controller\PassportBaseController;

class JobController extends PassportBaseController
{
	/**
	 * 更新用户工作信息
	 * @param $id passport id
	 * @param $data
	 */
	function create($data)
	{
		$result = array('message' => 'fail');
		if(empty($data['passport_id']) ) {
			return $this->renderJson($result);
		}

		$result = array('message' => 'fail');
		if($this->getDao('passport' , 'user')->updateUserJobs($data)) {
			$result['message'] = 'success';
		}
		return $this->renderJson($result);
	}
	/**
	 * 更新用户工作信息
	 * @param $id passport id
	 * @param $data
	 */
	function update($id , $data)
	{
		$result = array('message' => 'fail');
		if(empty($id) || empty($data['id']) ) {
			return $this->renderJson($result);
		}
		$data['passport_id'] = $id;
		$result = array('message' => 'fail');
		if($this->getDao('passport' , 'user')->updateUserJobs($data)) {
			$result['message'] = 'success';
		}
		return $this->renderJson($result);
	}
}