<?php
/**
 * 机构成员分组权限DAO
 * spring@2013-11-28
 */
namespace Portal\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
class OrgMemberGroupHasPrivilege extends BaseDao
{
	/**
	 * 获取指定$mgid机构成员分组 的权限
	 * @param int $mgid	成员分组id	
	 * @return Array
	 */
	public function getAllRecord($mgid,$page='',$num='')
	{
		$records = array();
	
		$where = 'org_member_group_id ='.$mgid;
		$records = $this->getList($where,$page,$num);
		
		return $records;
	}
	
	/**
	 * 获取指定$mgid机构成员分组 的权限 数目
	 * @param int $mgid	成员分组id	
	 * @return int
	 */
	public function getRecordCnt($mgid)
	{
		$count = 0;
		$where = 'org_member_group_id ='.$mgid;
		$count = $this->getCnt($where);
	
		return $count;
	}	
		
	/**
	 *  删除指定$id的成员
	 * @param int $id
	 * @return boolean
	 */
	public function deleteRecord($mgid)
	{
		$this->db->getDriver()->getConnection()->beginTransaction();
		try{
				
			$where = 'org_member_group_id = '.$mgid;
			$result = $this->realDelete($where);
			
			$this->db->getDriver()->getConnection()->commit();
	
			return $result;
		}catch(Exception $e){
			$this->db->getDriver()->getConnection()->rollback();
		}
	
	}
	
	/**
	 *  更新$id的成员
	 * @param int $id
	 */
	public function updateRecord(&$data,$mgid)
	{
		$this->db->getDriver()->getConnection()->beginTransaction();
		try{
			$where = 'org_member_group_id = '.$mgid;
			$result = $this->realDelete($where);
				 
			$privilege_arr = explode(',',$data['privilege']);
			$temp = array();
			$temp['org_member_group_id'] = $mgid;
			foreach($privilege_arr as $privilege) {
				$temp['org_privilege_code'] = $privilege;
				$result = $this->insert($temp);
			}			
			 
			$this->db->getDriver()->getConnection()->commit();
			 
		}catch(Exception $e){
			$this->db->getDriver()->getConnection()->rollback();
		}
		return $result;
	}
	
}
