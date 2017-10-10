<?php
/**
 * 品牌Dao
 * spring@2013-12-06
 */

namespace Common\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;

class CommonBrand extends BaseDao
{
	public function __construct($db,$sm)
	{
		parent::__construct($db,$sm);
		$this->table = 'common_brand';
	}
	

	/**
	 *   默认为当前dao对应表
	 * 返回满足条件的一行数据
	 * @param string $cond
	 * @param array columns
	 */
	public function getJoinOne($cond=1)
	{
		$adapter=$this->db;
	
		$table=$this->getTable();
	
		$sql = new Sql($adapter);
		$select=$sql->select();
		$select->from(array('b' => $table));
		
		$columns = array('*');
		if(!empty($columns)) {
			$select->columns($columns);
		}
		
		$select->where($cond)->limit(1);
		
		$select->join(
				array('d' => 'common_dict'),     // 使用别名联合表
				'b.brand_category_id = d.id',
				array('categoryname'=>'name','code'),
				$select::JOIN_LEFT
		);
		
	
		$selectString = $sql->getSqlStringForSqlObject($select);
	
		$records = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
	
		return $records[0];
	}
	
	/**
	 * 获取某个品牌信息	
	 * @param int $id	品牌id
	 * @param string $category	0直接输出	 1 输出品牌分类	
	 */
	public function getChoiceOne($id , $category = 0)
	{
		if($category == 0) {
			return $this->getOne("id =".$id);
		} else {
			return $this->getJoinOne("b.id =".$id);
		}
	}
	
	/**
	 *  获取所有的品牌信息	（一起输出品牌分类）	
	 * @param string $page	开始页数
	 * @param string $limit	 每页显示记录数
	 */
	public function getJoinList($page='',$limit='')
	{
		$adapter=$this->db;
	
		$table=$this->getTable();
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$select->from(array('b' => $table));
		
		$columns = array('*');
		if(!empty($columns))
			$select->columns($columns);
		
		$select->join(
				array('d' => 'common_dict'),     // 使用别名联合表				
				'b.brand_category_id = d.id',				
				array('categoryname'=>'name','code'),
				$select::JOIN_LEFT
		);
		
		if(!empty($page))
			$select->offset( ($page-1)*$limit);
		if(!empty($limit))
			$select->limit($limit);		
	
		$selectString = $sql->getSqlStringForSqlObject($select);
	
		return $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
	
	}
	
	/**	
	 * 获取所有的品牌信息
	 * @param string $category	0直接输出	 1 输出品牌分类
	 * @param string $page	
	 * @param string $limit
	 */
	public function getAllList($category = 0,$page = '',$limit = '')
	{
		if($category == 0) {
			return $this->getList(1,$page,$limit);
		} else {		
			return $this->getJoinList($page,$limit);
		}
	}

}
