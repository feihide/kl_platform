<?php
namespace CL\Cache;

/**
 *  redis cache class
 * @author ym
 */
class Rcache {

    private $_enable  = false;

    private $_config ;

    protected $_rcache ;


    #config contains: host , port , [timeout ,database]
    function __construct($config) {

        if(!extension_loaded('redis')) {
            $this->_enable = false;
            return ;
        }

        if(empty($config)) {
            $this->_enable = false;
            return ;
        }
        $this->_config = $config;

        try{

            $this->_rcache =new \Redis;
            if($this->_rcache->connect($config['host'],$config['port'],(empty($config['timeout']) ? 3: $config['timeout']) ) ) {
                $this->_enable = true;
                $this->select((empty($config['database']) ? '0' : $config['database']));
            }

        }catch (Exception $ex) {
            $this->_enable = false;
        }

    }

	public function enabled()
	{
		return $this->_enable;
	}

    public  function select($db) {
        if($this->_enable) {
            return $this->_rcache->select($db);
        }
        return null;
    }
    public  function set($key ,$val) {
        if($this->_enable) {
            return $this->_rcache->set($key , $val);
        }
        return null;
    }

    public  function get($key) {
        if($this->_enable) {
            return $this->_rcache->get($key);
        }
        return null;
    }

    #redis list 取出队列第一个
    public function lpop($key) {
        if($this->_enable) {
            return $this->_rcache->lpop($key);
        }
        return null;
    }

    #redis list 队列最后加入一个元素
    public function rpush($key , $value) {
        if($this->_enable) {
            return $this->_rcache->rpush($key , $value);
        }
        return null ;
    }

    //redis list 长度
    public function llen($key) {

        if($this->_enable) {
            return $this->_rcache->llen($key);
        }
        return null;
    }


    #redis hash 返回名称为h的hash中所有键对应的value
    function hvals($hashkey) {
        if($this->_enable) {
            return $this->_rcache->hVals($hashkey);
        }
        return null;
    }
    #redis hash 取出hashkey 下 为的key的所有元素
    function hget($hashkey ,$key) {
        if($this->_enable) {
            return $this->_rcache->hGet($hashkey,$key);
        }
        return null;
    }

    #redis hash set
    function hset($hashkey ,$key , $value) {
        if($this->_enable) {
            return $this->_rcache->hSet($hashkey, $key , $value);
        }
        return null;
    }

    #redis hash remove
    function hdel($hashkey ,$key) {
        if($this->_enable) {
            return $this->_rcache->hDel($hashkey,$key);
        }
        return null ;
    }

    #redis hash  element's count
    function hlen($hashkey) {
        if($this->_enable) {
            return $this->_rcache->hLen($hashkey);
        }
        return  null;
    }

    //删除key
    function del($key) {
        if($this->_enable) {
            return $this->_rcache->del($key);
        }
        return null ;
    }


}