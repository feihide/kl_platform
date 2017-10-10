<?php
namespace CL\Db;

class Mongodb
{
	private $mongo;
	static public $_readParent=false;
	private $db;
	private $collection;
	
	public function __construct($config)
	{
		try{
		$this->mongo=new \Mongo( $config['host'].':'.$config['port']);
		}
		catch ( \MongoConnectionException $e )
		{
			echo '<p>Couldn\'t connect to mongodb, is the "mongo" process running?</p>';
			exit();
		}
	}
	
	public function setDb($dbname){
		$this->db=$this->mongo->selectDB($dbname);
		return $this;
	}
	
	public function setCollection($c)
	{
		$this->collection= $this->db->selectCollection($c);
		return $this;
	}
	
	/**
	 *  $options 选项

		safe 是否返回操作结果信息
		
		fsync 是否直接插入到物理硬盘
	 * @param unknown $data
	 * @return unknown|boolean
	 */
	
	public function insert($data){
		$options =  array('safe' => True);
		if(!empty($this->collection)){
			$this->collection->insert($data, $options);
			return $data;
		}
		else
			return false;
	}
	
	/**
	 * option：safe 是否返回操作结果

					fsync 是否是直接影响到物理硬盘
					
					justOne 是否只影响一条记录
	 * @param unknown $cond
	 * @return Ambigous <boolean, multitype:>|boolean
	 */
	public function delete($cond){
		$options = array(’safe’=>true);
		if(!empty($this->collection))
			return $this->collection->remove($cond,$options);
		else
			return false;
	}
	
	/**
	 * option:
	 * safe 是否返回操作结果

		fsync 是否是直接影响到物理硬盘
		
		upsert 是否没有匹配数据就添加一条新的
		
		multiple 是否影响所有符合条件的记录，默认只影响一条
		更新子对象  $blog->update(
    array("comments.author" => "John"), 
    array('$set' => array('comments.$.author' => "Jim")));
	 * @param unknown $cond
	 */
	
	public function update($cond,$data){
		$options = array(’safe’=>true,’multiple’=>true);
		if(empty($this->collection))
			return false;
		return $this->collection->update($cond,$data,$options);
	}
	
	/**
	 * 获取数据
	 * MongoCursorMongoCollection::find(array $query,array $fields)

		array $query 条件
		$gt为大于、$gte为大于等于、$lt为小于、$lte为小于等于、$ne为不等于
		/mongo/  //相当于%%  查询name中以mongo开头的  /^mongo/})
		array $fields 要获得的字段
		返回一个游标记录对象MongoCursor。
		
		order=array('xxx'=>1,'yyy'=>-1);
	 */
    public function find($query=array(),$field=array(),$offset='',$limit='',$order=array()){
    	if(empty($this->collection))
    		return false;
    	$data=array();
    	$list=$this->collection->find($query,$field);
    	if(!empty($offset)){
    		$list=$list->skip($offset);
    	}

    	if(!empty($limit)){
    		$list=$list->limit($limit);
    	}
    	
    	if(!empty($order)){
    		$list=$list->order($order);
    	}
    	
    	foreach ($list as $k=>$v){
    		array_push($data,$v);
    	}
    	return $data;	
    }
	 
    public function findOne($query=array(),$field=array()){
    	if(empty($this->collection))
    		return false;
    	
    	return $this->collection->findOne();
    }
    
    
    /**
     * 获取满足条件的个数
     * @param unknown $query
     * @return boolean
     */
    public function count($query=array()){
    	if(empty($this->collection))
    		return false;
    	
    	return $this->collection->count($query);
    }
    
    /**
     * 定义索引
     *ps: array('address.country' => 1, 'sessions' => -1),
     */
    public function index($data){
    	return $this->collection->ensureIndex(
    			$data,
    			array('background' => true)
    	);
    }
    
    /**
     * 获取mongoId
     */
    public function getMongoID($string){
    	return new \MongoID($id_string);
    }
}