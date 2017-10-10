<?php
namespace Passport\Controller;

use CL\Controller\PassportBaseController;

/**
 * 用户找回密码
 */
define('RESET_PWD_TOKEN_EXPIRE' , 48 * 3600);#保存2天

class PasswordController extends PassportBaseController
{
	#找回密码
	function get($id)
	{
		if(empty($_GET['username'])) {#可以通过email username mobile 找回
			return $this->renderJson(array('message'=>'fail'));
		}
		$result = array('message' => 'fail');

		$dao = $this->getDao('passport' , 'passport');
		if(($data = $dao->fetchInfoByName($_GET['username'])) && '0' == $data['is_delete']) {
			$result['passport_id'] = $data['id'];
			if('1' == $data['login_type']) {
				$result['message'] = 'success';
			}else if('2' == $data['login_type'] && !empty($data['email_is_verified'])) {#已经验证
				$result['message'] = 'success';
			}else if('3' == $data['login_type'] && !empty($data['mobile_is_verified'])) {
				$result['message'] = 'success';
			}
		}
		if('success' == $result['message']) {
			#TODO  设置时效性
			$result['qs']['s'] = time();#开始时间
			$result['qs']['token'] = md5($data['passport_id'] .$result['qs']['s']. RESET_PWD_TOKEN_EXPIRE);
		}
		return $this->renderJson($result);
	}

	#重置‘找回密码’
	#id :1 登入后重置密码 需要提供旧密码 2：通过找回密码重置密码
	function update($id , $data)
	{
		if(empty($id) || empty($data['passport_id']) || !in_array($id , array('1','2'))) {
			return $this->renderJson(array('message'=>'fail'));
		}
		if('1' == $id ) {
			if(empty($data['pwd']) || empty($data['newpwd']) || $data['pwd'] ==$data['newpwd']) {
				return $this->renderJson(array('message'=>'fail'));
			}
			#检验旧密码是否正确
			if($rst =$this->getDao('passport','passport')->fetchPassportById($data['passport_id'] ,true)) {
				$chash = new \Zend\Crypt\Password\Bcrypt();
				if(!$chash->verify($data['pwd'] ,$rst['pwd'])) {#密码有误
					return $this->renderJson(array('message'=>'fail'));
				}
			}
		} else {
			if(empty($data['newpwd']) || empty($data['s']) || empty($data['token'])) {
				return $this->renderJson(array('message'=>'fail'));
			}
			if($data['token'] != md5($data['passport_id']. $data['s'] . RESET_PWD_TOKEN_EXPIRE) || time() - $data['s'] > RESET_PWD_TOKEN_EXPIRE) {
				#token 过期
				return $this->renderJson(array('message'=>'fail'));
			}
			$data['pwd'] = $data['newpwd'];
		}

		$result = array('message' => 'fail');

		$p = array('passport_id'=> $data['passport_id']);
		$p['pwd'] = $data['pwd'];

		if($this->getDao('passport' , 'passport')->updatePassport($p)) {
			$result['message'] = 'success';
		}
		return $this->renderJson($result);
	}

	# 删除 用户 机构
	function delete($id)
	{
		$result = array('message' => 'fail');
		if(empty($id)) {
			return $this->renderJson($result);
		}
		if($this->getDao('passport','user')->deletePassport($id)) {
			$result['message'] = 'success';
		}
		return $this->renderJson($result);
	}
}