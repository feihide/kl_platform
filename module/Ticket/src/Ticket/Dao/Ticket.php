<?php
namespace Ticket\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Adapter\Adapter;

/**
 * 获取 ticket id
 * @author ym
 */
define('TICKET_SQUEUE_IDS' ,'TICKET_SQUEUE_IDS_'); #预加载ticket id 队列名
define('TICKET_SQUEUE_REDIS_DB' ,1) ;#存储ticket序列的redis db index

class Ticket extends BaseDao
{

    #ticket_name :ticket标示名 必须值
    public function get($ticket_name)
    {
        if(empty($ticket_name)) {
            return false;
        }
        $rkey = TICKET_SQUEUE_IDS . strtoupper($ticket_name);
        if($redis = $this->sm->get('redis')) {
            $redis->select(TICKET_SQUEUE_REDIS_DB);
            if($ticket = $redis->lpop($rkey)) {
                return $ticket;
            }
        }

        $cl_config = $this->sm->get('cl_config');
        $dconfig = $cl_config['db']['ticket_server'];

        $_step = $_len = count($dconfig) ;

        $_pre_seed_step = 1000 * $_len ; #预先生成

        $sql = "insert into sequence (name) values ('{$ticket_name}') on DUPLICATE KEY update id=LAST_INSERT_ID(ID + {$_step}) ";
        if($this->db->createStatement($sql)->execute()) {

            $sql ="select LAST_INSERT_ID(id) AS maxid from sequence where name='{$ticket_name}' ";
            if($row = $this->db->query($sql,Adapter::QUERY_MODE_EXECUTE)->toArray()) {
                $_id = $maxid = $row[0]['maxid'];
                if($redis && $redis->enabled()){

					$redis->pipeline();
                    for($i=1; $i<= $_pre_seed_step ; $i++) {
                        $_id += $_step;
                        $redis->rpush($rkey , $_id);
                    }
					$redis->exec();

                    $sql ="update sequence set id={$_id}  where name='{$ticket_name}' and id={$maxid} ";
                    if(!$this->db->query($sql,Adapter::QUERY_MODE_EXECUTE)) {
                        $redis->del($rkey);#rollback redis squeue
                    }
                }
                return $maxid;
            }
        }
        return false;


    }
}