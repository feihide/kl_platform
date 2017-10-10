<?php
namespace CL\Dao;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
class BaseDao
{
	protected $db;
	protected $sm;
	protected $table;
	protected $apihost;
    protected $phoneapihost;

	protected $events = array(
			'get.precache',
			'get.pre',
			'get',
			'get.post',
			'get.postcache',
			'list.precache',
			'list.pre',
			'list',
			'list.post',
			'list.postcache',
			'create.pre',
			'create',
			'create.post',
			'save.pre',
			'save',
			'save.post',
			'remove.pre',
			'remove',
			'remove.post');

	public function __construct($db,$sm)
	{
		$this->db=$db;
		$db->query("set names 'utf8'" , $db::QUERY_MODE_EXECUTE);
		$this->sm=$sm;
		$name=explode('\\',get_class($this));
		$name=array_pop($name);

        if($_COOKIE['apihost']){
           $this->apihost = $_COOKIE['apihost'];
        }
        else
            $this->apihost =  $this->sm->get('cl_config')['params']['api_domain'];

        if(isset($_COOKIE['phoneapihost'])){
            $this->phoneapihost = $_COOKIE['phoneapihost'];
        }
        else
            $this->phoneapihost =  $this->sm->get('cl_config')['params']['phone_api_domain'];

		$name=preg_split("/(?=[A-Z])/",lcfirst($name));
		$this->table='';
		foreach($name as $item){
			if($this->table)
				$this->table.='_';

			$this->table.=lcfirst($item);
		}
	}

	public function api($method, $url, $data = false)
	{
	    $curl = curl_init();
	    $url='http://'.$this->sm->get('cl_config')['params']['api_domain'].urldecode($url);
	    // Optional Authentication:
	    //  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    //  curl_setopt($curl, CURLOPT_USERPWD, "username:password");

	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	    switch ($method)
	    {
	    	case "POST":
	    	    curl_setopt($curl, CURLOPT_POST, 1);

	    	    if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	    	    break;
	    	case "PUT":
	    	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
	    	    if (!empty($data)) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

	    	    break;
	    	case "DELETE":
	    	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

	    	    if (!empty($data)) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));


	    	    break;
	    	default://GET
	    	    if ($data)
	    	       $url = sprintf("%s?&%s", $url, http_build_query($data));

	    }
	    curl_setopt($curl, CURLOPT_URL, $url);

	    $data  = curl_exec($curl);
	    curl_close($curl);

	    $result=json_decode($data,true);
        $result['status'] =1;
        if(empty($result)){
            print_r($data);
        }
	    else{
            if($result['code']==200){
                $result['status']=0;
            }
	        return $result['data'];
        }
	}


	public function callapi($method, $url, $data = false)
    {
    	$curl = curl_init();
        $data = array_merge($data,array('device_type'=>'test'));

        $url='http://'.$this->apihost.urldecode($url);
    	// Optional Authentication:
    	//  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    	//  curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    	switch ($method)
    	{
    		case "POST":
    			curl_setopt($curl, CURLOPT_POST, 1);

    			//if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

    			break;
    		case "PUT":
    			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");

    			if (!empty($data)) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    			break;
    		case "DELETE":
    			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

    			if (!empty($data)) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

    			break;
    		default://GET
    			if ($data)
    				$url = sprintf("%s?&%s", $url, http_build_query($data));

    	}
    	curl_setopt($curl, CURLOPT_URL, $url);

    	$result = curl_exec($curl);
		curl_close($curl);
        return $result;
    }

    public function callphoneapi($method, $url, $data = false)
    {
        $curl = curl_init();

				if( substr($this->phoneapihost, 0, 4) != "http"){
        $url='http://'.$this->phoneapihost.urldecode($url);
			}
			else{
				$url=$this->phoneapihost.urldecode($url);
			}
        //$data = array_merge($data,array('device_type'=>'test'));
        if(isset($data['access_token'])){
            $accessToken= $data['access_token'];
        }
	if( substr($url, 0, 5) == "https"){
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
}
$headers = array("Content-type: application/x-www-form-urlencoded",
		//"Accept: application/json",
		"Cache-Control: no-cache","Pragma: no-cache"
		);
if($_COOKIE['orgin']){
	array_push($headers,"origin:".$_COOKIE['orgin']);
}



        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Optional Authentication:
        //  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //  curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                 //$url+='?access_token='.$accessToken;
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");

                if (!empty($data)) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

                if (!empty($data)) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

                break;
            default://GET
                if ($data)
                    $url = sprintf("%s?&%s", $url, http_build_query($data));

        }

        curl_setopt($curl, CURLOPT_URL, $url);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }



	public function trigger($event, $target = null, $argv = array(), $callback = null)
	{
		return  $this->sm->get('Application')->getEventManager()->trigger($event, $target, $argv, $callback);
	}

	protected function getTable()
	{
		return $this->table;
	}

	/** 事例
	 * SELECT * FROM `listings` WHERE `listings_id` = (SELECT MAX(`listings_id`) FROM `listings`)
	 Tried this code:
	 $select = new Select();
	 $select->from($this->tableName);
	 $expression = new Expression(sprintf('MAX(%s)', $platform->quoteIdentifier($this->listingsId)));
	 $subSelect = new Select();
	 $subSelect->from($this->tableName)->columns(array($expression));
	 $where = new Where();
	 $where->equalTo($this->listingsId, $subSelect);
	 $select->where($where);
	 echo $select->getSqlString($platform);
	 */

	/**
	 *   默认为当前dao对应表
	 * 返回满足条件的一行数据
	 * @param string $cond
	 * @param array columns
	 */
	public function getOne($cond=1,$columns=array())
	{
		$adapter=$this->db;

		$table=$this->getTable();

		$sql = new Sql($adapter);
		$select=$sql->select()->from($table)->where($cond)->limit(1);
		if(!empty($columns))
			$select->columns($columns);

		$selectString = $sql->getSqlStringForSqlObject($select);

		$records = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();

		return  isset($records[0])?$records[0]:array();
	}

	/**
	 *   默认为当前dao对应表
	 * 返回满足条件的数据数量
	 * @param string $cond
	 */
	public function getCnt($cond=1)
	{
		$adapter=$this->db;

		$table=$this->getTable();

		$sql = new Sql($adapter);
		$select=$sql->select()->from($table)->where($cond);

		$expression = new Expression(sprintf('COUNT(%s) as num', '*'));

		$select->columns(array($expression));

		$selectString = $sql->getSqlStringForSqlObject($select);


		$records = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
		return $records[0]['num'];

	}
	/**
	 *  默认为当前dao对应表
	 * @param number $cond
	 * @param string $page
	 * @param string $limit
	 * @param unknown $columns
	 * @param string $order
	 */
	public function getList($cond=1,$page='',$limit='',$order='',$columns=array(),$joinTable=array(),$joinCond='',$joinType='left')
	{
		$adapter=$this->db;

		$table=$this->getTable();
		$sql = new Sql($adapter);
		$select=$sql->select()->from($table)->where($cond);
		if(!empty($columns))
			$select->columns($columns);
		if(!empty($page))
			$select->offset( ($page-1)*$limit);
		if(!empty($limit))
			$select->limit($limit);
		if(!empty($order))
			$select->order($order);

		if(!empty($joinTable)){
			$select->join($joinTable, $joinCond, $joinType);
		}

		$selectString = $sql->getSqlStringForSqlObject($select);

		return $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();

	}

	/**
	 *
	 * @param unknown $data  插入的数据
	 * @param string $table 插入的表名    默认为当前dao 对应表
	 */
	public function insert($data,$table='')
	{
		$adapter=$this->db;
		if(empty($table))
			$table=$this->getTable();

		$sql = new Sql($adapter);
		$insert=$sql->insert()->into($table)->values($data);

		$insertString = $sql->getSqlStringForSqlObject($insert);

		return $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
	}

	/**
	 *
	 * @param unknown $data 更新数据
	 * @param unknown $cond  搜索条件
	 */
	public function update($data,$cond,$table='')
	{
		$adapter=$this->db;
		if(empty($table))
			$table=$this->getTable();
		$sql = new Sql($adapter);

		$update=$sql->update()->table($table)->set($data)->where($cond);

		$updateString = $sql->getSqlStringForSqlObject($update);
		return $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
	}


	/**
	 *   默认为当前dao对应表
	 * 所有表均为假删除，实际操作为更新is_delete字段，如果没有该字段需加上
	 * @param unknown $cond
	 */
	public function delete($cond)
	{
		$adapter=$this->db;

		$table=$this->getTable();
		$sql = new Sql($adapter);

		$update=$sql->update()->table($table)->set(array('is_delete'=>1))->where($cond);

		$updateString = $sql->getSqlStringForSqlObject($update);
		return $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
	}

	/**
	 * 真删除
	 */
	public function realDelete($cond){
		$adapter=$this->db;
		$table=$this->getTable();
		$sql = new Sql($adapter);
		$delete=$sql->delete()->from($table)->where($cond);

		$deleteString = $sql->getSqlStringForSqlObject($delete);
		return $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
	}


    #获取数组中key对应的value
    public function defaultMaps($data , $key , $default = null)
    {
        if(empty($data) || !is_array($data)) {
            return $data;
        }
        return isset($data[$key]) ? $data[$key] : $default;
    }

    #获取ticket id
    public function getTicketId($ticket_name)
    {
	    return $this->sm->get('dao_factory')->getDao('ticket','ticket')->get($ticket_name);
    }

	#获取memcache object
	public function getMcache()
	{
		return $this->sm->get('mcache');
	}
	#获取redis object
	public function getRcache()
	{
		return $this->sm->get('redis');
	}

	/*
	 * 获取SphinxClient对象
	 * 使用参考：
	 * $obj = $this->getSphinx();
    	$result = $obj->search('欧洁', 'apparatus1',5,0,true,'len',array(35));
	 */
	public function getSphinx()
	{
		return $this->sm->get('sphinx');
	}

}
