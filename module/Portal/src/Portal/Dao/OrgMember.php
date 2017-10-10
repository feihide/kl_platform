<?php
/**
 * 机构成员DAO
 * spring@2013-11-26
 */
namespace Portal\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
class OrgMember extends BaseDao
{
	private $memberGroupIdArr = array();
			
	/**
	 * 获取某个机构所有成员分组 id数组
	 * @param int $org_id	机构id	
	 */
	private function getMemberGroupIdArr($org_id)
	{	
		if(empty($this->memberGroupIdArr)){
			$where = 'passport_id = '.$org_id;
			$this->table = 'org_member_group';
			$records1 = $this->getList($where,'','','',array('id'));//获取某个机构所有成员分组
			
			if($records1) {	
				
				foreach($records1 as $k => $v){
					$this->memberGroupIdArr[] = $v['id'];	//机构所有成员分组 id数组
				}			
			}
		}
		return $this->memberGroupIdArr;
	}
	
	/**
	 * 获取某机构下的所有的成员
	 * @param int $org_id	机构id
	 * @param int $mgid	机构成员分组id
	 * @param string $page
	 * @param string $num
	 * @return Array
	 */
	public function getAllRecord($org_id,$mgid=0,$page='',$num='')
	{
		$records = array();
		if ($mgid == 0) {
			$member_group_id_arr = $this->getMemberGroupIdArr($org_id);
			$member_group_id_str = join(',',$member_group_id_arr);
			$this->table = 'org_member';
			$where = 'org_member_group_id in ('.$member_group_id_str.')';
			$records = $this->getList($where,$page,$num,'ctime desc');		

		} else {//获取某个机构某成员分组下的机构成员
			$this->table = 'org_member';
			$where = 'org_member_group_id ='.$mgid;
			$records = $this->getList($where,$page,$num,'ctime desc');
		}		

		//成员基本信息
		foreach($records as &$item){
			$passport = $this->sm->get('dao_factory')->getDao('passport','passport')->fetchPassportById($item['passport_id']);
			if(!empty($passport)) {
				$item = array_merge($item,$passport);
			}
		}
		//机构成员服务类型
		$this->table = 'org_member_has_service_type';
		foreach($records as &$item){
			$where = 'passport_id ='.$item['passport_id'];
			$service_type['service_type'] = $this->getList($where);
			if(!empty($service_type)) {
				$item = array_merge($item,$service_type);
			}
		}
		
		
		return $records;
	}
	
	/**
	 * 获取某机构下的所有的成员数目
	 * @param int $org_id	机构id
	 * @param int $mgid	机构成员分组id
	 * @return int	
	 */
	public function getRecordCnt($org_id,$mgid)
	{
		$count = 0;
		if ($mgid == 0) {
			$member_group_id_arr = $this->getMemberGroupIdArr($org_id);
			$member_group_id_str = join(',',$member_group_id_arr);
			$this->table = 'org_member';
			$where = 'org_member_group_id in ('.$member_group_id_str.')';
			$count = $this->getCnt($where);
			
		} else {//获取某个机构某成员分组下的机构成员
			$this->table = 'org_member';
			$where = 'org_member_group_id ='.$mgid;
			$count = $this->getCnt($where);
		}
		return $count;
	}
	
	/**
	 *  获$id的成员	
	 * @param int $id
	 * @return array
	 */
	public function getOneRecord($id)
	{
		$record1 = $this->getOne('passport_id = '.$id);
    	$record2 = $this->sm->get('dao_factory')->getDao('passport','passport')->fetchPassportById($id);
    	
    	$record = array();
    	if (!empty($record1) && !empty($record2)) {
    		$record = array_merge($record1,$record2);
    	} elseif(!empty($record1)) {
    		$record = $record1;
    	} elseif(!empty($record2)) {
    		$record = $record2;
    	}
    	
    	//机构成员服务类型
    	$this->table = 'org_member_has_service_type';
    	$where = 'passport_id ='.$record['passport_id'];
    	$service_type['service_type'] = $this->getList($where);
    	if(!empty($service_type)) {
    		$record = array_merge($record,$service_type);
    	}
    	
    
    	return $record;
	}
	
	/**
	 *  插入成员信息
	 * @param array $data
	 * @return boolean
	 */
	public function insertRecord(&$data1,&$data2)
	{
		$this->db->getDriver()->getConnection()->beginTransaction();
		try{
			$result = $this->insert($data1);
			
			if(!empty($data2)) {//有服务类型
				$this->table = 'org_member_has_service_type';	
	
				$type_id_arr = explode(',',$data2['service_type_str']);
				$temp = array();
				$temp['passport_id'] = $data2['passport_id'];
				foreach($type_id_arr as $type_id) {
					$temp['service_type_id'] = $type_id;				
					$result = $this->insert($temp);
				}
			}
			
			$this->db->getDriver()->getConnection()->commit();
			
		}catch(Exception $e){
			$this->db->getDriver()->getConnection()->rollback();
		}	
		return $result;
		
	}
	
	/**
	 *  更新$id的成员	
	 * @param int $id	
	 */
	public function updateRecord(&$data1,&$data2,$id)
	{
    	$this->db->getDriver()->getConnection()->beginTransaction();
    	try{
    		$where = 'passport_id = '.$id;
			$result = $this->update($data1,$where);
    			
    		if(!empty($data2)) {//有服务类型
    			$this->table = 'org_member_has_service_type';
    			
    			$where = 'passport_id = '.$id;
    			$result = $this->realDelete($where);
    	
    			$type_id_arr = explode(',',$data2['service_type_str']);
    			$temp = array();
    			$temp['passport_id'] = $id;
    			foreach($type_id_arr as $type_id) {
    				$temp['service_type_id'] = $type_id;
    				$result = $this->insert($temp);
    			}
    		}
    			
    		$this->db->getDriver()->getConnection()->commit();
    			
    	}catch(Exception $e){
    		$this->db->getDriver()->getConnection()->rollback();
    	}
    	return $result;
	}
	
	/**
	 *  删除指定$id的成员	
	 * @param int $id
	 * @return boolean
	 */
	public function deleteRecord($id)
	{
		$this->db->getDriver()->getConnection()->beginTransaction();
		try{
			
			$where = 'passport_id = '.$id;
			$result = $this->delete($where);
				
			//删除机构成员服务类型
			$this->table = 'org_member_has_service_type';
			$where = 'passport_id = '.$id;
			$result = $this->realDelete($where);
			
			$this->sm->get('dao_factory')->getDao('passport','passport')->deletePassport($id);
			
			$this->db->getDriver()->getConnection()->commit();
				
			return $result;
		}catch(Exception $e){
			$this->db->getDriver()->getConnection()->rollback();
		}
		
	}
}
