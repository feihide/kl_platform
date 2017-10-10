<?php
namespace Passport\Controller;

use CL\Controller\PassportBaseController;

class UserController extends PassportBaseController
{

	function get($id)
	{
		$dao = $this->getDao('passport','passport');
		if($dao && ($data = $dao->fetchPassportById($id))) {
			$dao = $this->getDao('passport','user');
			$data['detail'] = $dao->fetchUserDetailById($id);
			$data['edus']   = $dao->fetchUserEdusById($id);#教育信息
			$data['jobs']   = $dao->fetchUserJobsById($id);#工作履历
		}
		return $this->renderJson($data);
	}

	function getList()
	{

	}
	/**
	 * 注册C端新用户
	 * @param $data
	 * @return mixed
	 * @author ym
	 */
	function create($data)
	{

		if(empty($data)) {
			return $this->renderJson(array('message'=>'fail'));
		}

		$data['type'] = 0;

		$result = array('message' => 'fail');
		if($this->getDao('passport' , 'passport')->addPassport($data)) {
			$result['message'] = 'success';
		}
		return $this->renderJson($result);
	}

	/**
	 * 更新用户基本信息
	 * @param $id passport id
  	 * @param $data
	 */
	function update($id , $data)
	{
		$result = array('message' => 'fail');
		if(empty($id) ) {
			return $this->renderJson($result);
		}
		$data['passport_id'] = $id;
		$result = array('message' => 'fail');
		if($this->getDao('passport' , 'user')->updateUser($data)) {
			$result['message'] = 'success';
		}
		return $this->renderJson($result);
	}

	function delete($id)
	{
		$result = array('message' => 'fail');
		if(empty($id)) {
			return $this->renderJson($result);
		}

		if($this->getDao('passport' , 'passport')->deletePassport($id)) {
			$result['message'] = 'success';
		}
		return $this->renderJson($result);

	}
}