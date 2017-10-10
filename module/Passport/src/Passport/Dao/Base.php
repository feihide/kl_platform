<?php
namespace Passport\Dao;

use CL\Dao\BaseDao;

define('PASSPORT_BASE_LIST_PRE' ,'PASSPORTS_LIST_' );
define('PASSPORT_DETAIL_LIST_PRE' ,'PASSPORTS_DETAIL_LIST_' );

class Base extends BaseDao
{
	private $redis_key_match = 'base';

	protected function getRedisKeyMatch()
	{
		return strtolower($this->redis_key_match);
	}

	public function setRedisKeyMatch($str)
	{
		$this->redis_key_match = $str;
	}

	private function getKeyAlias()
	{
		return array('1' =>'username' ,'2'=>'email','3'=>'mobile','4'=>'at_name','5'=>'passport_id');
	}
	public function ckey($type)
	{
		$alias  = $this->getKeyAlias();
		if(empty($alias[$type])) {
			return false;
		}
		$_key = strtoupper($alias[$type]);
		$_pre = strtoupper(sprintf('passport_%s_list_pre',$this->getRedisKeyMatch()));
		if(defined($_pre)) {
			return  $_pre.'_'.$_key;
		}
		return false;
	}
	/**
	 * 将数据push / get redis
	 * @param $name
	 * @param $type
	 * @param bool $data
	 * @return bool|mixed
	 * @author ym
	 */
	public  function mixPassportData($mix ,$type , $data = false )
	{
		if(empty($mix)) {
			return false;
		}

		if(!($key = $this->ckey($type))) {
			return false;
		}

		if(empty($data)) { #get
			if($redis = $this->getRcache()) {
				# data format: name ,pwd ,other (json)
				if($data = $redis->hget($key , $mix)) {
					return json_decode($data ,true);
				}
			}
			return false;
		}
		# set
		if($redis = $this->getRcache()) {
			$redis->hset($key , $mix ,json_encode($data));
			if('base' == $this->getRedisKeyMatch() && $type =='5' && $mix == $data['passport_id']) {
				#循环将 email mobile at_name username 加入队列
				if($alias = $this->getKeyAlias()) {
					$object = json_encode(array('passport_id' =>$data['passport_id'] ));
					foreach($alias as $k => $v) {
						if($k == $type) {
							break;
						}
						if(($key = $this->ckey($k)) && !empty($data[$v])) {
							$redis->hset($key , $data[$v] ,$object);
						}
					}
				}
			}
		}
		return false;
	}
	#删除redis cache
	public function delPassportData($mix , $type )
	{
		if(!($key = $this->ckey($type))) {
			return false;
		}
		if($redis = $this->getRcache()) {
			return $redis->hdel($key , $mix);
		}
		return false;
	}

	/**
	 * 检查登入name的格式类型
	 * @param $value
	 * @return int  1：username login ，2：email login 3:mobile login
	 */
	public  function checkNameType($value)
	{
		$type = 0;

		$filter =new \Zend\Validator\EmailAddress();
		if($filter->isValid($value)) {
			$type = 2;
		}
		if(!$type) {
			$filter =new \Zend\Validator\Digits();
			if($filter->isValid($value)) {
				$type = 3;
			}
		}

		return !$type ? 1 : $type; # 1：username login ，2：email login 3:mobile login;
	}
}