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

class CommonDict extends BaseDao
{
    public function __construct($db,$sm)
    {
        parent::__construct($db,$sm);
		
    }
	

	/**
	 * 获取某个dict信息	
	 * @param string $cond	条件	
	 */
    public function getJoinOne($cond=1)
    {
        $adapter=$this->db;
	
		$table=$this->getTable();
	
		$sql = new Sql($adapter);
		$select=$sql->select();
		$select->from(array('d' => $table));
		
		$columns = array('*');
		if(!empty($columns)) {
			$select->columns($columns);
		}
		
        $select->where($cond)->limit(1);		
	
		$select->join(
				array('m' => 'common_dict_meta'),     // 使用别名联合表
				'd.id = m.dict_id',
				array('k','v'),
				$select::JOIN_LEFT
        );
		
	
        $selectString = $sql->getSqlStringForSqlObject($select);
	
        $records = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
	
        return $records[0];
    }
	
	/**
	 *  获取所有的dict信息	（一起与dict_meta输出）	
	 * @param string $cond	条件
	 * @param string $page	开始页数
	 * @param string $limit	 每页显示记录数
	 */
    public function getJoinList($cond=1,$page='',$limit='')
    {
        $adapter=$this->db;
	
        $table=$this->getTable();
        $sql = new Sql($adapter);
        $select = $sql->select();
		
        $select->from(array('d' => $table));
		
        $columns = array('*');
        if(!empty($columns))
            $select->columns($columns);
		
        $select->join(
                array('m' => 'common_dict_meta'),     // 使用别名联合表				
                'd.id = m.dict_id',				
                array('k','v'),
                $select::JOIN_LEFT
        );
		
        $select->where($cond);
		
        if(!empty($page))
            $select->offset( ($page-1)*$limit);
        if(!empty($limit))
            $select->limit($limit);		
	
        $selectString = $sql->getSqlStringForSqlObject($select);
	
        return $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
	
    }


}
