<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id: UserController.php 37 2013-11-22 08:57:19Z quanwei $
 */
				 	
namespace Portal\Controller;
use CL\Controller\PortalBaseController;
	
class PrivilegeController extends PortalBaseController
{	
    /**
     * http get
     */
    public function get($id)
    {	

    	$data=$this->getDao('portal','orgGroupHasPrivilege')->getOne('passport_id='.$id);
        return $this->renderJson($data);
    }


    /**
     * http get list
     */
    public function getList()
    {
    	$gid=$this->params('group_id');
    	$request=$this->getRequest()->getQuery()->toArray();
    	$data=$this->getDao('portal','orgGroupHasPrivilege')->getList('org_group_id='.$gid,$request['page'],$request['num']);
        $num=$this->getDao('portal','orgGroupHasPrivilege')->getCnt('org_group_id='.$gid);
        
    	return $this->renderJson(array('list'=>$data,'num'=>$num));
    }

    /**
     * http post
     */
    public function create($data)
    {	
    	$pri=explode(',',$data['privilege']);
    	$gid=$this->params('group_id');
    	
    	$this->getDao('portal','orgGroupHasPrivilege')->realDelete('org_group_id='.$gid);
    	foreach($pri as $p){
    		$data=array('org_group_id'=>$gid,'org_privilege_code'=>$p);
    		$result=$this->getDao('portal','orgGroupHasPrivilege')->insert($data);
    	}
    	
    	return $this->renderJson(array());
   	}

    /**
     * http put
     * 更新角色权限，全部清除在添加
     */
    public function update($id, $data)
    {
    	return $this->renderJson(array());
    }

    /**
     * http delete
     */
    public function delete($id)
    {
    	//$result=$this->getDao('portal','org')->delete('passport_id='.$id);
         return $this->renderJson();

    }
}
