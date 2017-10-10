<?php
/**
* @date: 2013-11-30
* @author: feihide
* @version: 
*/

namespace Portal\Controller;
use CL\Controller\BaseController;

class ServiceController extends BaseController
{
    /**
     * http get
     */
    public function get($id)
    {	
    	$data=$this->getDao('portal','OrgService')->getOne('id='.$id);

        return $this->renderJson($data);
    }


    /**
     * http get list
     */
    public function getList()
    {
    	$request=$this->getRequest()->getQuery()->toArray();
    	
    	$org_id=$this->params('org_id');
    	
    	$data=$this->getDao('portal','OrgService')->getList('passport_id='.$org_id,$request['page'],$request['num'],'ctime desc');
        $num=$this->getDao('portal','OrgService')->getCnt('passport_id='.$org_id);
        
    	return $this->renderJson(array('list'=>$data,'num'=>$num));
    }

    /**
     * http post
     */
    public function create($data)
    {	
    	$id=$this->getTicketId('service_id');
    	
    	$data=array_merge($data,array('id'=>$id,'ctime'=>time(),'utime'=>time()));
    	
    	$result=$this->getDao('portal','OrgService')->insert($data);
    	
         return $this->renderJson($result);
    }

    /**
     * http put
     */
    public function update($id, $data)
    {
    	$result=$this->getDao('portal','OrgService')->update($data,'id='.$id);
        return $this->renderJson(array());
    }

    /**
     * http delete
     */
    public function delete($id)
    {
    	$result=$this->getDao('portal','OrgService')->delete('id='.$id);
         return $this->renderJson();

    }
}
