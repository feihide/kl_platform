<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id: UserController.php 37 2013-11-22 08:57:19Z quanwei $
 */

namespace Portal\Controller;
use CL\Controller\PortalBaseController;

class OrgController extends PortalBaseController
{
    /**
     * http get
     */
    public function get($id)
    {	
    	$person = array(
    			'name' => 'Cesar Rodas',
    			'email' => 'crodas@php.net',
    			'address' => array(
    					array(
    							'country' => 'PY',
    							'zip' => '2160',
    							'address1' => 'foo bar'
    					),
    					array(
    							'country' => 'PY',
    							'zip' => '2161',
    							'address1' => 'foo bar bar foo'
    					),
    			),
    			'sessions' => 0,
    	);
    	/**
    	$collection=$this->getMongo()->setDb('u')->setCollection('list');
		
    	$person=$collection->find();
    	print_r($person);exit;
    	 */
    	$db=$this->getMongo()->setDb('u');
    	$grid = $db->getGridFS();
    	
    	$id = $grid->storeFile("6.jpg");
    	$game = $grid->findOne();
    	
    	// add a downloads counter
    	$game->file['downloads'] = 0;
    	$grid->save($game->file);
    	 
    	
    	/**
    	$events =$this->getEventManager();
    	
    	$eventOutput="在事件处理函数中填充！";

    	$events->attach('do', function($e) use(&$eventOutput) {
    		$event  = $e->getName();
    		$target = $e->getTarget();
    		$params = $e->getParams();
    		$eventOutput=sprintf(
    				'处理的事件是 "%s", 目标"%s" , 携带的参数是 %s',
    				$event,$target,
    				json_encode($params)
    		);
    	});
    	$params = array('foo' => 'bar', 'baz' => 'bat');
    	$events->trigger('do', 'EventManager', $params);
    	echo $eventOutput;exit;
    	*/
    	$data2=$this->getDao('passport','passport')->fetchPassportById($id);
    	if(!empty($data2) && $data2['is_delete']==0){
    		$data1=$this->getDao('portal','org')->getId('passport_id='.$id);
    	
    		$data=array_merge($data1,$data2);
    	}
    	else
    		$data=array();
        return $this->renderJson($data);
    }

    
    /**
     * http get list
     */
    public function getList()
    {
    	$request=$this->getRequest()->getQuery()->toArray();
    	if(isset($request['gid']))
    		$gid=$request['gid'];
    	else
    		$gid=0;
    	$data=$this->getDao('portal','org')->getList('org_group_id='.$gid . ' and is_delete = 0',$request['page'],$request['num'],'ctime desc'); // add is_delete, added by Ethan
        $num=$this->getDao('portal','org')->getCnt('org_group_id='.$gid);
        
        if($num){
        	foreach($data as &$item){
        		$passport=$this->getDao('passport','passport')->fetchPassportById($item['passport_id']);
        		
        		$item=array_merge((array)$item, (array)$passport); // 强制转换成array，避免warning，Ethan添加
        	}
        }
        
    	return $this->renderJson(array('list'=>$data,'num'=>$num));
    }

    /**
     * http post
     */
    public function create($data)
    {
        $data = array_merge($data, array('passport_id' => $this->getDao('passport', 'passport')->getTicketId('passport'))); // added by Ethan        
    	$result=$this->getDao('portal','org')->insert($data);
    	
         return $this->renderJson($result);
    }

    /**
     * http put
     */
    public function update($id, $data)
    {
    	$result=$this->getDao('portal','org')->update($data,'passport_id='.$id);
        return $this->renderJson(array());

    }

    /**
     * http delete
     */
    public function delete($id)
    {
    	$this->getDao('passport','passport')->delete('id='.$id);
    	$result=$this->getDao('portal','org')->delete('passport_id='.$id);
         return $this->renderJson();

    }
}
