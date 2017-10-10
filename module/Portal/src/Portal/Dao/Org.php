<?php
namespace Portal\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
class Org extends BaseDao
{
	public function getId($id){
		return $this->getOne($id);
	}
	
	/**
	 * 新增 机构
	 * @param $data
	 * @return mixed
	 * @author ym
	 */
	function addOrg($data)
	{
		$this->db->getDriver()->getConnection()->beginTransaction();
		$p = array(
			$data['passport_id'] ,
			$this->defaultMaps($data , 'brand_id' ,0),
			$this->defaultMaps($data , 'admin_name'),
			$this->defaultMaps($data , 'area_id' ,0), #xx市XX区
			$this->defaultMaps($data , 'parent_id' ,0),
			$this->defaultMaps($data , 'biaodi_id' ,0),
			$this->defaultMaps($data , 'address' ,''),
			$this->defaultMaps($data , 'website' ,''),
			$this->defaultMaps($data , 'tel' ,0),
			$this->defaultMaps($data , 'introduction' ,''),
			0,
			time(),
			time()
		);
		$sql = ' insert into org (passport_id,brand_id,admin_name,area_id,parent_id,biaodi_id,address,website,tel,introduction,is_delete,ctime) ' ;
		$sql .= ' values (?,?,?,?,?,?,?,?,?,?,?,?)';
		if($this->db->query($sql , $p)) {
			$this->db->getDriver()->getConnection()->commit();
			return true;
		}
		$this->db->getDriver()->getConnection()->rollback();
		return false;
	}

	/**
	 * 更新 机构管理员基本信息
	 * @param  $data
	 * @author ym
	 */
	function updateOrg($data)
	{
		if(empty($data['passport_id'])) {
			return false;
		}
		$sql = '';
		$p = array();
		if(isset($data['at_name'])) {
			$sql.='at_name=?,';
			$p[] = $data['at_name'] ;
		}
		if(isset($data['area_id'])) {
			$sql.='area_id=?,';
			$p[] = $data['area_id'] ;
		}
		if(isset($data['address'])) {
			$sql.='address=?,';
			$p[] = $data['address'] ;
		}
		if(isset($data['tel'])) {
			$sql.='tel=?,';
			$p[] = $data['tel'] ;
		}
		if(isset($data['website'])) {
			$sql.='website=?,';
			$p[] = $data['website'] ;
		}
		if(isset($data['introduction'])) {
			$sql.='introduction=?,';
			$p[] = $data['introduction'] ;
		}
		if(empty($sql)) {
			return false;
		}

		$isok = false;
		$sql .='utime=?';
		$p[] = time();
		$p[] = $data['passport_id'] ;
		$this->db->getDriver()->getConnection()->beginTransaction();
		$sql = 'update org set '.trim($sql,',').' where passport_id=?' ;
		if($this->db->query($sql,$p)) {
			$isok = true;
			if(isset($data['portal_code']) || isset($data['at_name']) || isset($data['mobile']) || isset($data['email'])) {
				if(!$this->sm->get('dao_factory')->getDao('passport','passport')->updatePassport($data)) {
					$isok =  false;
				}
			}
		}
		if($isok) {
			$this->db->getDriver()->getConnection()->commit();
			return true;
		}
		$this->db->getDriver()->getConnection()->rollback();
		return false;
	}
}
