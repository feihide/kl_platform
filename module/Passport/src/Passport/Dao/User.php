<?php
namespace Passport\Dao;

/**
 * 普通用户 model
 */

class User extends Base
{
	/**
	 * 更新用户 基本信息
	 * @param $data
	 * @return bool
	 * @author ym
	 */
	function updateUser($data) {

		if(empty($data['passport_id'])) {
			return false;
		}

		$sql ='';
		$p = array();

		if(isset($data['birth'])) {
			$sql .='birth=?,';
			$p['birth'] = $data['birth'];
		}
		if(isset($data['sex'])) {
			$sql .='sex=?,';
			$p['sex'] = $data['sex'];
		}
		if(isset($data['industry_id'])) {
			$sql .='industry_id=?,';
			$p['industry_id'] = $data['industry_id'];
		}
		if(isset($data['ismarried'])) {
			$sql .='ismarried=?,';
			$p['identity'] = $data['ismarried'];
		}
		if(isset($data['income_status'])) {
			$sql .='income_status=?,';
			$p['income_status'] = $data['income_status'];
		}
		if(isset($data['msn'])) {
			$sql .='msn=?,';
			$p['msn'] = $data['msn'];
		}
		if(isset($data['qq'])) {
			$sql .='qq=?,';
			$p['qq'] = $data['qq'];
		}
		if(isset($data['introduction'])) {
			$sql .='introduction=?,';
			$p['introduction'] = $data['introduction'];
		}
		if(empty($sql)) {
			return false;
		}

		$p['passport_id'] = $data['passport_id'];
		$sql .='passport_id=?,';
		$this->db->getDriver()->getConnection()->beginTransaction();

		$isok = false;

		$msql  = 'insert into passport_detail ('.implode(',',array_keys($p)).',ctime,utime) values ('.str_pad('',count($p)*2 ,'?,',STR_PAD_LEFT).time().','.time().')';
		$msql .= ' on duplicate key update '.$sql.'utime='.time();
		if($this->db->query($msql,array_merge(array_values($p),array_values($p)))) {
			$isok = true;
			if(isset($data['portal_code']) || isset($data['at_name']) || isset($data['mobile'])) {
				if(!$this->sm->get('dao_factory')->getDao('passport','passport')->updatePassport($data)) {
					$isok =  false;
				}
			}
		}
		if($isok){
			//COMMIT
			$this->db->getDriver()->getConnection()->commit();
			#reset cache
			$this->fetchUserDetailById($data['passport_id'] , true);
		} else {
			$this->db->getDriver()->getConnection()->rollback();
		}
		return $isok;
	}

	#获取基本信息
	function fetchUserDetailById($passport_id , $force_from_db = false)
	{
		if(empty($passport_id)) {
			return false;
		}
		$this->setRedisKeyMatch('detail');
		if(!$force_from_db && ($data = $this->mixPassportData($passport_id , 5))) {
			return json_decode($data ,true);
		}

		$sql = 'select passport_id ,birth,sex,ismarried,income_status,industry_id,msn,qq,introduction from passport_detail ';
		$sql .= ' where passport_id=?  limit 1';

		if($data = $this->db->query($sql , array($passport_id))->toArray()) {
			$this->mixPassportData($passport_id , 5 ,$data);
			return $data;
		}
		return false;
	}

	function fetchUserDetailByIds($mixids)
	{
		if(empty($mixids)) {
			return false;
		}
		$max_limit = 100;

		$mixids = is_array($mixids) ? $mixids : array($mixids);
		$data = array();
		$mixids = array_slice($mixids , 0 ,$max_limit);#limit fetch nums
		foreach($mixids as $id) {
			$data[$id] = $this->fetchUserDetailById($id);
		}
		return $data;
	}

	#新增 更新 前端用户教育信息
	function updateUserEdus($data)
	{
		if(empty($data['passport_id'])) {
			return false;
		}
		$data['id'] = !empty($data['id']) ? $data['id'] : $this->getTicketId('passport_edu');
		if(empty($data['id'])) {
			return false;
		}
		$sql ='';
		$p = array();
		if(isset($data['school_id'])) {
			$sql .='school_id=?,';
			$p['school_id'] = $data['school_id'];
		}
		if(isset($data['school_department_id'])) {
			$sql .='school_department_id=?,';
			$p['school_department_id'] = $data['school_department_id'];
		}
		if(isset($data['year'])) {
			$sql .='year=?,';
			$p['year'] = $data['year'];
		}
		if(isset($data['privacy'])) {
			$sql .='privacy=?,';
			$p['privacy'] = $data['privacy'];
		}
		if(empty($sql)) {
			return false;
		}
		$p['id'] = $data['id'];
		$p['passport_id'] = $data['passport_id'];
		$sql .= 'id=?,passport_id=?,';

		$this->db->getDriver()->getConnection()->beginTransaction();

		$sqlm  = 'insert into passport_edu ('.implode(',',array_keys($p)).',ctime,utime) values ('.str_pad('',count($p) *2,'?,',STR_PAD_LEFT).time().','.time().')';
		$sqlm .= 'on duplicate key update '.$sql.'utime='.time();
		if($this->db->query($sqlm ,array_merge(array_values($p),array_values($p)))) {
			$this->db->getDriver()->getConnection()->commit();
			return true;
		} else {
			$this->db->getDriver()->getConnection()->rollback();
			return false;
		}
	}

	#获取教育信息
	function fetchUserEdusById($passport_id , $force_from_db = false)
	{
		if(empty($passport_id)) {
			return false;
		}
		$this->setRedisKeyMatch('edu');
		if(!$force_from_db && ($data = $this->mixPassportData($passport_id , 5))) {
			return json_decode($data ,true);
		}

		$sql = 'select id,passport_id,school_id,school_department_id,year,privacy from passport_edu ';
		$sql .= ' where passport_id=? ';

		if($data = $this->db->query($sql , array($passport_id))->toArray()) {
			$this->mixPassportData($passport_id , 5 ,$data);
			return $data;
		}
		return false;
	}

	#新增 更新 前端用户工作信息
	function updateUserJobs($data)
	{
		if(empty($data['passport_id'])) {
			return false;
		}

		$data['id'] = !empty($data['id']) ? $data['id'] : $this->getTicketId('passport_job');
		if(empty($data['id'])) {
			return false;
		}
		$sql ='';
		$p = array();
		if(isset($data['city_id'])) {
			$sql .='city_id=?,';
			$p['city_id'] = $data['city_id'];
		}
		if(isset($data['name'])) {
			$sql .='name=?,';
			$p['name'] = $data['name'];
		}
		if(isset($data['start_year']) && strtotime($data['start_year'].'-01-01')) {
			$sql .='start_year=?,end_year=?,';
			$p['start_year'] = $data['start_year'];
			if(isset($data['end_year']) && strtotime($data['end_year'].'-01-01')) {
				$p['end_year'] = $data['end_year'];
			} else {
				$p['end_year'] = '0';
			}
		}
		if(isset($data['position'])) {
			$sql .='position=?,';
			$p['position'] = $data['position'];
		}
		if(empty($sql)) {
			return false;
		}
		$p['id'] = $data['id'];
		$p['passport_id'] = $data['passport_id'];
		$sql .='id=?,passport_id=?,';

		$this->db->getDriver()->getConnection()->beginTransaction();

		$sqlm  = 'insert into passport_job ('.implode(',',array_keys($p)).',ctime,utime) values ('.str_pad('',count($p)*2,'?,' ,STR_PAD_LEFT).time().','.time().')';
		$sqlm .= 'on duplicate key update '.$sql.'utime='.time();
var_dump($sqlm , array_merge(array_values($p),array_values($p)));
		if($this->db->query($sqlm ,array_merge(array_values($p),array_values($p)))) {
			$this->db->getDriver()->getConnection()->commit();
			return true;
		} else {
			$this->db->getDriver()->getConnection()->rollback();
			return false;
		}
	}

	#获取工作信息
	function fetchUserJobsById($passport_id , $force_from_db = false)
	{
		if(empty($passport_id)) {
			return false;
		}
		$this->setRedisKeyMatch('job');
		if(!$force_from_db && ($data = $this->mixPassportData($passport_id , 5))) {
			return json_decode($data ,true);
		}

		$sql = 'select id,passport_id,city_id,name,start_year,end_year,position from passport_job ';
		$sql .= ' where passport_id=? ';

		if($data = $this->db->query($sql , array($passport_id))->toArray()) {
			$this->mixPassportData($passport_id , 5 ,$data);
			return $data;
		}
		return false;
	}
}