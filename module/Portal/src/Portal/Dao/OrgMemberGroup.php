<?php
/**
 * 机构成员分组DAO
 * spring@2013-11-28
 */
namespace Portal\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
class OrgMemberGroup extends BaseDao
{
	/**
	 * 获取某个机构所有成员分组
	 * @param int $org_id	机构id
	 * @param string $page
	 * @param string $num
	 * @return Array
	 */
	public function getAllRecord($org_id,$page='',$num='')
	{
		$records = array();

		$where = 'passport_id ='.$org_id;
		$records = $this->getList($where,$page,$num);
		 
		return $records;
	}
	
	/**
	 * 获取某机构下的所有的成员分组数目
	 * @param int $org_id	机构id
	 * @return int	
	 */
	public function getRecordCnt($org_id)
	{
		$count = 0;		
		$where = 'passport_id ='.$org_id;
		$count = $this->getCnt($where);
		
		return $count;
	}
	
	/**
	 *  获得某$id的成员分组	
	 * @param int $id
	 * @return array
	 */
	public function getOneRecord($id)
	{
		$record = $this->getOne('id = '.$id);
    	
    	return $record;
	}
	
	/**
	 *  插入机构成员分组信息
	 * @param array $data
	 * @return boolean
	 */
	public function insertRecord(&$data)
	{
		$id = $this->getTicketId('org_member_group');
		
		$data = array_merge($data,array('id' => $id));
		
		$result = $this->insert($data);
		return $result;
		
	}
	
	/**
	 *  更新$id的成员	
	 * @param int $id	
	 */
	public function updateRecord(&$data,$id)
	{
		$where = 'id = '.$id;
		$result = $this->update($data,$where);
    	return $result;
	}
	
	/**
	 *  删除指定$id的成员	
	 * @param int $id
	 * @return boolean
	 */
	public function deleteRecord($id)
	{
		$where = 'id = '.$id;
		$result = $this->delete($where);
    	return $result;
	}
}
