<?php
/**
 * 机构成员分组权限
 * spring@2013-11-29
 */

namespace Portal\Controller;
use CL\Controller\PortalBaseController;

class MemberPrivilegeController extends PortalBaseController
{
    /**
     * http get
     */
    public function get($id)
    {	
    	
    }


    /**
     * http get list
     * 取得指定$mgid机构成员分组 的权限
     */
    public function getList()
    {
    	$page = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
    	$num = isset($_REQUEST['num'])?intval($_REQUEST['num']):0;
    	
    	$org_id = $this->params('org_id');
    	$mgid = $this->params('group_id');
    	 
    	$data = $this->getDao('portal','orgMemberGroupHasPrivilege')->getAllRecord($mgid,$page,$num);    	 
    	$count = $this->getDao('portal','orgMemberGroupHasPrivilege')->getRecordCnt($mgid);
    	
    	return $this->renderJson(array('list'=>$data,'count'=>$count));
    }

    /**
     * http post
     */
    public function create($data)
    {	
    	$org_id = $this->params('org_id');    	 
    	
    	$pri = explode(',',$data['privilege']);
    	$mgid = $this->params('group_id');
    	
    	$this->getDao('portal','orgMemberGroupHasPrivilege')->realDelete('org_member_group_id ='.$mgid);
    	foreach($pri as $p){
    		$data = array('org_member_group_id'=>$mgid,'org_privilege_code'=>$p);
    		$result = $this->getDao('portal','orgMemberGroupHasPrivilege')->insert($data);
    	}
    	
    	return $this->renderJson(array());
   	}

    /**
     * http put
     * 更新成员分组权限，全部清除再添加
     */
    public function update($id, $data)
    {
    	$org_id = $this->params('org_id');
    	
    	$mgid = $this->params('group_id');
    	   
    	$result = $this->getDao('portal','orgMemberGroupHasPrivilege')->updateRecord($data,$mgid);
    	
    	return $this->renderJson(array());
    }

    /**
     * http delete
     */
    public function delete($id)
    {
    	$org_id = $this->params('org_id');
    	$mgid = $this->params('group_id');
    	 
    	 
    	$result = $this->getDao('portal','orgMemberGroupHasPrivilege')->deleteRecord($mgid);
         return $this->renderJson();

    }
}
