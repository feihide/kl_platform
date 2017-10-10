<?php
namespace Platform\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
class  ConsoleLog extends BaseDao
{
	public function getLogList($cond){
	    return $this->api('GET', '/system/conlog',$cond);
	}
	
}
