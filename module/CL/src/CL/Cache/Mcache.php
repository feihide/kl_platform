<?php
namespace CL\Cache;

/**
 *  _mcache cache class
 * @author ym
 */

class Mcache {

    private $_enable  = false;

    private $_config ;

    protected $_mcache ;

    #config contains:host ,port ,[persistent,weight,timeout]
    function __construct($config) {

        if(!extension_loaded('memcached')) {
            $this->_enable = false;
            return ;
        }

        if(empty($config)) {
            $this->_enable = false;
            return ;
        }
        $this->_config = $config;

        $this->_mcache=  new \Memcached;

        $this->_enable = true;

        foreach ($config as $item) {
            if (isset($item["host"])) {
                $this->_mcache->addServer($item["host"], $item["port"], (empty($item["weight"]) ? 1 : $item['weight']));
            }
        }
//        if(false === @$this->_mcache->set('ping','ping.server',0,30)) {
//            $this->_enable = false;
//        }else  if (!empty($_GET['clear_mcache']) && 'blabla' == $_GET['clear_mcache']) {
//            $this->_enable = 0;#针对get 失效
//        }

    }

    function set($key, $var, $expire = 300) {
        if(false !== $this->_enable) {
            //为了不同时失效，增加随机时间
            $expire += rand(1, 20);
            return $this->_mcache->set($key, $var, $expire);
        }
        return null ;
    }

    function real_set($key, $var, $flag = 0, $expire = 300) {
        if(false !== $this->_enable) {
            return $this->_mcache->set($key, $var, $expire);
        }
        return null;
    }

    function replace($key, $var, $flag = 0, $expire = 300) {
        if(false !== $this->_enable) {
            return $this->_mcache->replace($key, $var,  $expire);
        }
        return null;
    }


    function increment($key, $value = 1) {
        if(false !== $this->_enable) {
            return $this->_mcache->increment($key, $value);
        }
        return null ;
    }


    function get($key, $flags = 0) {

        if ($this->_enable) {
            return $this->_mcache->get($key);
        }
        return null;
    }


    function delete($key, $timeout = 0) {
        if(false !== $this->_enable) {
            return $this->_mcache->delete($key, $timeout);
        }
        return null;
    }

    function __destruct() {
        if($this->_mcache)	$this->_mcache->quit();
    }

}