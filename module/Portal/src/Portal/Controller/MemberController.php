<?php
/**
 * 机构成员controller
 * spring@2013-11-26
 */

namespace Portal\Controller;
use CL\Controller\PortalBaseController;

class MemberController extends PortalBaseController
{
    /**
     * http get
     */
    public function get($id)
    {	
    	$org_id = $this->params('org_id');
    	
    	$data = $this->getDao('portal','orgMember')->getOneRecord($id);//获取$id的成员
    	
        return $this->renderJson($data);
    }


    /**
     * http get list
     * 取得指定$id机构,$mgid 机构成员分组 的成员列表
     */
    public function getList()
    {
    	$page = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
    	$num = isset($_REQUEST['num'])?intval($_REQUEST['num']):0;
    	$mgid = isset($_REQUEST['mgid'])?intval($_REQUEST['mgid']):0;
    	
    	$org_id = $this->params('org_id');
    	
    	$data = $this->getDao('portal','orgMember')->getAllRecord($org_id,$mgid,$page,$num);//获取某机构下的所有的成员 
    	
    	$count = $this->getDao('portal','orgMember')->getRecordCnt($org_id,$mgid);//获取某机构下的所有的成员 数目

    	return $this->renderJson(array('list'=>$data,'count'=>$count));
    }

    /**
     * http post
     * 创建机构成员信息
     */
    public function create($data)
    {	
    	$org_id = $this->params('org_id');
    	if(isset($data['service_type_str'])) {//有服务类型
    		$data1 = array_diff_key($data, array('service_type_str' => ''));     	
    		$data2 = array_intersect_key($data, array('passport_id' => '','service_type_str' => '')); 	//服务类型
    		
    	} else {
    		$data1 = $data;
    		$data2 = array();
    	}
    	$data1 = array_merge($data1,array('ctime' => time(),'utime' => time(),'is_delete' => 0));
    	
	    $result = $this->getDao('portal','orgMember')->insertRecord($data1,$data2);
        return $this->renderJson($result);
    }

    /**
     * http put
     * 更新指定$id机构的指定$mid的成员
     */
    public function update($id, $data)
    {
    	$org_id = $this->params('org_id');
    	if(isset($data['service_type_str'])) {//有服务类型
    		$data1 = array_diff_key($data, array('service_type_str' => ''));
    		$data2 = array_intersect_key($data, array('service_type_str' => '')); 	//服务类型
    	
    	} else {
    		$data1 = $data;
    		$data2 = array();
    	}
		
    	$data1 = array_merge($data1,array('utime' => time()));
    	$result = $this->getDao('portal','orgMember')->updateRecord($data1,$data2,$id);
        return $this->renderJson(array());

    }

    /**
     * http delete
     * 删除指定$id机构的指定$mid的成员
     */
    public function delete($id)
    {
    	$org_id = $this->params('org_id');
    	
    	$result = $this->getDao('portal','orgMember')->deleteRecord($id);
         return $this->renderJson();

    }
}
