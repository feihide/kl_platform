<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id: UserController.php 37 2013-11-22 08:57:19Z quanwei $
 */

namespace Portal\Controller;
use CL\Controller\PortalBaseController;

class GroupController extends PortalBaseController
{
    /**
     * http get
     */
    public function get($id)
    {	
    	
    	$data=$this->getDao('portal','OrgGroup')->getOne('id='.$id);
        return $this->renderJson($data);
    }


    /**
     * http get list
     */
    public function getList()
    {	
    	$request=$this->getRequest()->getQuery()->toArray();
    	$org_id=$this->params('org_id');
    	$where='passport_id='.$org_id . ' AND is_delete = 0';//check is_delete, by Ethan
    	$data=$this->getDao('portal','OrgGroup')->getList($where,$request['page'],$request['num'],'ctime desc');
        $num=$this->getDao('portal','OrgGroup')->getCnt($where);
        
    	return $this->renderJson(array('list'=>$data,'num'=>$num));
    }

    /**
     * http post
     */
    public function create($data)
    {	
    	$org_id=$this->params('org_id');
    	$id = $this->getDao('portal','OrgGroup')->getTicketId('org_group');
    	
    	$data=array_merge($data,array('passport_id'=>$org_id,'id'=>$id,'ctime'=>time()));
    	
        $result=$this->getDao('portal','OrgGroup')->insert($data);

        // added by Ethan
        if ($result->getAffectedRows() == 1) { //添加成功
            $status = 0; 
        } else { // 添加失败
            $status = 1;
        }

         return $this->renderJson($data, $status);
    }

    /**
     * http put
     */
    public function update($id, $data)
    {
    	$result=$this->getDao('portal','OrgGroup')->update($data,'id='.$id);
        return $this->renderJson(array());

    }

    /**
     * http delete
     */
    public function delete($id)
    {
        $result=$this->getDao('portal','OrgGroup')->delete('id='.$id);

        // added by Ethan
        if ($result->getAffectedRows() > 0) { //删除成功
            $status = 0;
        } else {
            $status = 1;
        }

         return $this->renderJson(array(), $status);
    }
}
