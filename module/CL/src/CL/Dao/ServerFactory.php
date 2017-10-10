<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CL\Dao;
class ServerFactory
{
	protected $sm;
	protected $serverList;
	protected $cityDb;
	protected $config;
	
    public function __construct($sm)
    {
   		$this->sm=$sm;
   		$this->serverList=array();
   		//分城市的库
   		$this->cityDb=array('portal');
   		$this->config=$this->sm->get('cl_config');
    }
    
    public function getServer($dbname)
    {
 		if(!in_array($dbname,$this->serverList)){
	    	if(in_array($dbname,$this->cityDb)){
	    		$city=isset($_GET['portal_code'])?$_GET['portal_code']:'sh';
	    		$serverConfig =$this->config['db'][$dbname.'_server'][$city];
	    	}
	    	else{
	    		$serverConfig =$this->config['db'][$dbname.'_server'];
	    	}
	    	
	    	$server = $serverConfig[mt_rand(0, (count($serverConfig)-1))];
	   		$server=array_merge($server,array('driver' => 'Pdo_Mysql'));
	   		$this->serverList[$dbname]=$server;
 		}
 		else{
 			$server=$this->serverList[$dbname];
 		}
   		return  new \Zend\Db\Adapter\Adapter($server);
    }
}
