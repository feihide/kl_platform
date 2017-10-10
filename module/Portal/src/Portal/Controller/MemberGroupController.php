<?php
/**
 * 机构成员分组controller
 * spring@2013-11-28
 */

namespace Portal\Controller;
use CL\Controller\PortalBaseController;

class MemberGroupController extends PortalBaseController
{
    /**
     * http get
     */
    public function get($id)
    {	
    	$org_id = $this->params('org_id');

    	$data = $this->getDao('portal','orgMemberGroup')->getOneRecord($id);//获取$id的成员

    	return $this->renderJson($data);
    }


    /**
     * http get list
     * 取得指定$id机构 的成员分组列表
     */
    public function getList()
    {
    	$page = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
    	$num = isset($_REQUEST['num'])?intval($_REQUEST['num']):0;

    	$org_id = $this->params('org_id');
    	
    	$data = $this->getDao('portal','orgMemberGroup')->getAllRecord($org_id,$page,$num);//获取某机构下的成员分组
    	
    	$count = $this->getDao('portal','orgMemberGroup')->getRecordCnt($org_id);//获取某机构下的所有的成员分组 数目

    	return $this->renderJson(array('list'=>$data,'count'=>$count));
    }

    /**
     * http post
     * 创建机构成员分组
     */
    public function create($data)
    {	
    	$org_id = $this->params('org_id');
    	
    	$data = array_merge($data,array('passport_id' => $org_id,'is_delete' => 0));
    	
	    $result = $this->getDao('portal','orgMemberGroup')->insertRecord($data);
        return $this->renderJson($result);
    }

    /**
     * http put
     * 更新指定$id成员分组
     */
    public function update($id, $data)
    {
    	$org_id = $this->params('org_id');
    	$data = array_merge($data,array('passport_id' => $org_id));
    	
    	$result = $this->getDao('portal','orgMemberGroup')->updateRecord($data,$id);
        return $this->renderJson(array());

    }

    /**
     * http delete
     * 删除指定$id成员分组
     */
    public function delete($id)
    {
    	$org_id = $this->params('org_id');
    	
    	$result = $this->getDao('portal','orgMemberGroup')->deleteRecord($id);
         return $this->renderJson();

    }
}
