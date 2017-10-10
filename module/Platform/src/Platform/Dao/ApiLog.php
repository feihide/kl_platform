<?php
namespace Platform\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
class  ApiLog extends BaseDao
{
	public function getStaticList($param){
	    return $this->api('GET', '/log/api_static',$param);
	}

    public function getDetailList($param){
        return $this->api('GET', '/log/api_detail',$param);
    }

    public function getErrorList($param){
        return $this->api('GET', '/log/api_error',$param);
    }

}
