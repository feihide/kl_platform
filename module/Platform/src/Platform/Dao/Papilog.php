<?php
namespace Platform\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
class  Papilog extends BaseDao
{
	public function getLogList($param,$offset,$limit){
	    $db =new Mon();
        return $db->setDb('common_mongo')->setCollection('papi_log')->getList($param,array(),$offset,$limit,array('ctime'=>-1));
	}

    public function getLogNum($param){
        $db =new Mon();
        return $db->setDb('common_mongo')->setCollection('papi_log')->getNum($param);
    }

    public function delete($data){
        $db = new Mon();
        return $db->setDb('common_mongo')->setCollection('papi_log')->delete($data);
    }

}

class Mon
{
    private $mongo;
    static public $_readParent=false;
    private $db;
    private $collection;
    private $dbName;
    private $mongoConsume =array();
    private $tableName;
    private $slaveMongo;

    public function __construct()
    {
        $config=array('host'=>'192.168.0.230','port'=>27017);
        $this -> slaveMongo = $this -> getMongo($config);
    }

    private function getMongo($config){
        try{
            $this->mongo=new \MongoClient( $config['host'].':'.$config['port']);
        }
        catch ( \MongoConnectionException $e )
        {
            echo '<p>Couldn\'t connect to mongodb, is the "mongo" process running?</p>';
            echo '<p> mongo host is'.$config['host'].',mongo port is'.$config['port'];
            exit();
        }
        return $this;
    }

    public function setDb($dbname){
        $this->db=$this->mongo->selectDB($dbname);
        $this->dbName = $dbname;
        if(!isset($this->mongoConsume[$this->dbName])){
            $this->mongoConsume[$this->dbName] = array('write'=>array(),'read'=>array());
        }
        return $this;
    }

    public function setCollection($c)
    {
        $this->tableName = $c;
        $this->collection= $this->db->selectCollection($c);
        return $this;
    }

    public function getGridFS(){
        return $this->db->getGridFS();
    }

    /**
     *  $options 选项

    safe 是否返回操作结果信息

    fsync 是否直接插入到物理硬盘
     * @param unknown $data
     * @return unknown|boolean
     */

    public function create($data){
        $start = $this->getMicrotime();

        $options =  array('w' => True,'fsync'=>true);
        if(!empty($this->collection)){

            $this->collection->insert($data, $options);
            $this->setAnalysis('create',$data,$start);

            if( strtolower( PLATFORM ) == 'admin' ){
                $log = array();
                $log['admin_id'] = USER;
                $log['db'] = 'mongo';
                $log['tb'] = $this->tableName;
                $log['action'] = 'insert';
                $log['controller'] = basename( MY_CONTROLLER );
                $log['data'] = $data;
                //$log['cond'] = $cond;
                $log['ctime'] = time();
                //$this->sm->get('service_factory')->getService('admin_log')->getJRDMongo( 'admin_log' )->create( $log );
            }
            unset($start,$data,$options);
            return true;
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
        $start = $this->getMicrotime();
        $options = array('w'=>true,'fsync'=>true);
        if(!empty($this->collection)){
            $r =  $this->collection->remove($cond,$options);
            $this->setAnalysis('delete',$cond,$start);
            if( strtolower( PLATFORM ) == 'admin' && $r ){
                $log = array();
                $log['admin_id'] = USER;
                $log['db'] = 'mongo';
                $log['tb'] = $this->tableName;
                $log['action'] = 'realdelete';
                $log['controller'] = basename( MY_CONTROLLER );
                //$log['data'] = $data;
                $log['cond'] = $cond;
                $log['ctime'] = time();
                // $this->sm->get('service_factory')->getService('admin_log')->getJRDMongo( 'admin_log' )->create( $log );
            }
            return $r;
        }
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
     *
     * return
     * Array
    (
    [err] =>
    [updatedExisting] => 1
    [n] => 1
    [ok] => 1
    )
     */

    public function update($cond,$data,$options){
        $start = $this->getMicrotime();

        $r = $this->collection->update($cond,$data,$options);
        if( strtolower( PLATFORM ) == 'admin' && $r ){
            $log = array();
            $log['admin_id'] = USER;
            $log['db'] = 'mongo';
            $log['tb'] = $this->tableName;
            $log['action'] = isset( $data['is_delete'] ) && $data['is_delete'] == 1 ? 'delete' : 'update';
            $log['controller'] = basename( MY_CONTROLLER );
            $log['data'] = $data;
            $log['cond'] = $cond;
            $log['ctime'] = time();
            //$this->sm->get('service_factory')->getService('admin_log')->getJRDMongo( 'admin_log' )->create( $log );
        }
        $this->setAnalysis('update',array('data'=>$data,'cond'=>$cond),$start);
        return $r;
    }


    public function edit($cond,$data){
        $options = array('w' =>true,'multiple' =>true,'upsert'=>true);
        if(empty($this->collection))
            return false;
        $data=array('$set' =>$data);

        $r= $this->update($cond,$data,$options);
        return $r['ok']?$r['n']:false;
    }

    /**
     * 向数组字段添加元素
     * @param $cond 条件
     * @param $field 插入的字段名
     * @param $update 其他更新数据
     */
    public function push($cond,$field,$data,$update = array()){
        $options = array('w' =>true,'multiple' =>true,'upsert'=>true);
        if(empty($this->collection))
            return false;
        if(!empty($update))
            $data=array('$push' =>array($field =>$data),'$set'=>$update);
        else
            $data=array('$push' =>array($field =>$data));
        $r= $this->update($cond,$data,$options);

        return $r['ok']?$r['n']:false;
    }

    /**
     * 删除指定元素
     */
    public function pull($cond,$delete){
        $options = array('w' =>true,'multiple' =>true,'upsert'=>true);
        $r = $this->update($cond,array('$pull'=>$delete),$options);
        return $r['ok']?$r['n']:false;
    }


    /**
     * 统计数据累加
     * @param $id 变更的ID
     * @param array $field  累加的字段
     * @param $int  $add 累加值
     * @return boolean
     */
    public function inc($cond,$field,$add=1){
        $update=array();
        foreach($field as $item){
            $update[$item] = $add;
        }
        $options = array('w' =>true,'multiple' =>true,'upsert'=>true);
        $r= $this->update($cond,array('$inc'=>$update),$options);
        return $r['ok']?true:false;
    }

    /**
     * 获取多条数据
     * MongoCursorMongoCollection::find(array $query,array $fields)

    array $query 条件
    $gt为大于、$gte为大于等于、$lt为小于、$lte为小于等于、$ne为不等于
    /mongo/  //相当于%%  查询name中以mongo开头的  /^mongo/})
    array $fields 要获得的字段
    返回一个游标记录对象MongoCursor。

    db.blog.find(
    　　{
    　　　　"comments":
    　　　　{
    　　　　　　"$elemMatch":
    　　　　　　{
    　　　　　　　　"author":"refactor",
    　　　　　　　　"score":{"$gte":5}
    　　　　　　}
    　　　　}
    　　})
    "$elemMatch"将限定条件进行分组,仅当需要对一个内嵌文档的多个键操作时才会用到.

    order=array('xxx'=>1,'yyy'=>-1);
     */
    public function getList($query=array(),$field=array(),$offset='',$limit='',$order=array()){
//        $this -> slaveMongo-> setDb($this -> dbName);
//        $this -> collection = $this -> db -> selectCollection($this -> tableName);
          $start = $this->getMicrotime();

        if(empty($this->collection))
            return false;
        $data=array();


        $list = $this->collection->find($query,$field);
        if(!empty($offset)){
            $list = $list->skip($offset);
        }

        if(!empty($limit)){
            $list = $list->limit($limit);
        }

        if(!empty($order)){
            $list = $list->sort($order);
        }

        foreach ($list as $k=>$v){
            array_push($data,$v);
        }

        $this->setAnalysis('find',array('query'=>$query,'field'=>$field,'offset'=>$offset,'limit'=>$limit,'order'=>$order),$start);

        return $data;
    }

    /**
     * 获取一条记录
     * @param unknown $query
     * @param unknown $field
     * @return boolean
     */
    public function get($query=array(),$field=array()){
        $this -> slaveMongo-> setDb($this -> dbName);
        $this -> collection = $this -> db -> selectCollection($this -> tableName);
        $start = $this->getMicrotime();

        if(empty($this->collection))
            return false;
        $field=array_merge($field,array('_id'=>0));
        $r =  $this->collection->findOne($query,$field);
        $this->setAnalysis('findone',array('query'=>$query,'field'=>$field),$start);
        return $r;
    }

    /**
     * 获取满足条件的个数
     * @param unknown $query
     * @return boolean
     */
    public function getNum($query=array()){
        $start = $this->getMicrotime();

        if(empty($this->collection))
            return false;

        $r = $this->collection->count($query);
        $this->setAnalysis('count',array('query'=>$query),$start);

        return $r;
    }

    /**
     * command
     */
    public function command($collection,$map,$reduce,$out){
        return $this->db->command(array(
            "mapreduce" => "people",
            "map" => $map,
            "reduce" => $reduce,
            "out" => "countries"
        ));
    }

    /**
     * 定义索引
     *  db.places.ensureIndex( { loc : "2d" } )默认的，Mongo假设你索引的是经度/维度，因此配置了一个从-180到180的取值范围
     */
    public function index($data){
        return $this->collection->ensureIndex(
            $data,   //array('address.country' => 1, 'sessions' => -1),  1: asc  -1:desc
            array('background' => true) //'unique' => true
        );
    }

    /**
     * 获取某字段唯一值列表
     * @return array
     */
    public function distinct($field,$cond){
        $start = $this->getMicrotime();


        $r =  $this->collection->distinct($field, $cond);
        $this->setAnalysis('distinct',array('query'=>$cond,'field'=>$field),$start);

        return $r;
    }

    public function group($keys,$initial,$reduce,$opt){
        $start = $this->getMicrotime();

        $r = $this->collection->group($keys,$initial,$reduce,$opt);
        $this->setAnalysis('group',array('keys'=>$keys,'initial'=>$initial,'reduce'=>$reduce,'opt'=>$opt),$start);

        if($r['ok']){
            return $r['retval'];
        }
        else{
            return false;
        }
    }

    /**
     * 聚合
     */
    public function  aggregate($opt){
        $start = $this->getMicrotime();

        $r = $this->collection->aggregate($opt);
        $this->setAnalysis('aggregate',array('opt'=>$opt),$start);
        if($r['ok'])
            return $r['result'];
        else
            return array();
    }

    /**
     * 获取mongoId
     */
    public function getMongoID($string){
        return new \MongoID ($string);
    }

    public function __destruct(){
        if(isset($this->mongo)) {
            $connections = $this->mongo->getConnections();

            foreach ( $connections as $con )
            {
                // echo "Closing '{$con['hash']}': ";
                $closed =  $this->mongo->close( $con['hash'] );
                // echo $closed ? "ok" : "failed", "\n";
            }
        }
    }

    public function getAnalysis(){
        return $this->mongoConsume;
    }


    public function setAnalysis($type,$data,$start){
        if(!defined('CONSOLE')){
            $mainType='read';
            if(in_array($type,array('create','update','delete'))){
                $mainType = 'write';
            }

            $this->mongoConsume[$this->dbName][$mainType][]=array('sql'=>array('table'=>$this->tableName, 'type'=>$type,'data'=>$data),'consume'=>$this->getMicrotime()-$start);
        }
    }


    private function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

}