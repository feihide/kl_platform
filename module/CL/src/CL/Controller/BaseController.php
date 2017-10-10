<?php
/**
 * 基类控制器，存放公共方法，如果只对应单个模块，在继承该类生成单个模块对应的基类controller
 * 方法最好都是protected
 */
namespace CL\Controller;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
class BaseController extends AbstractRestfulController{
	//TODO  优化缺省当前模块名
	protected function getDao($module,$dao)
	{
		return $this->getServiceLocator()->get('dao_factory')->getDao($module,$dao );
	}
	
	protected function getMongo()
	{
		return $this->getServiceLocator()->get('mongo');
	}
	
	
	protected function renderJson($result=array(),$status=0){
		$data=array('status'=>$status,'data'=>$result);
		return new JsonModel($data);
	}
	
	protected function getTicketId($ticket_name){
		return $this->getDao('ticket','ticket')->get($ticket_name);
	}
	
}
