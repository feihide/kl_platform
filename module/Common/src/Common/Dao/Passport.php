<?php
namespace User\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Adapter\Adapter;
class Passport extends BaseDao
{

    public function getList()
    {
        $sql='select * from passport ';
        $r=$this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $data=array();
        return $row = $r->current();

    }
}
