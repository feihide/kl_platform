<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CL\Dao;

class DaoFactory
{	
	protected $dbAdapter;
	protected $sm;
	protected $daoList;
	
    public function __construct($sm)
    {
   		$this->sm=$sm;
   		$this->daoList=array();
    }
    
    public function getDao($module,$dao)
    {
    	$name= '\\'.ucfirst($module).'\\Dao\\'.ucfirst($dao);
  
    	$this->dbAdapter=$this->sm->get('server_factory')->getServer($module);
    	if(!isset($this->daoList[$name])){
    		$this->daoList[$name]= new $name($this->dbAdapter,$this->sm);
    	}
    	return $this->daoList[$name];
    }
}
