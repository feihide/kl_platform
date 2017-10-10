<?php
/**
 * dict controller
 * spring@2013-11-26
 */

namespace Common\Controller;

use CL\Controller\CommonBaseController;

class DictController extends CommonBaseController
{
	
    /**
     * 获取某个$id数据
     */
    public function get($id)
    {    	
    	$record = $this->getDao('common','commonDict')->getJoinOne("d.id =".$id);
    	
    	return $this->renderJson($record);
    }
    
    
    /**
     * 获取列表数据
     */
    public function getList()
    {
    	$page = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
    	$num = isset($_REQUEST['num'])?intval($_REQUEST['num']):0;
    
		$type = isset($_REQUEST['type'])?intval($_REQUEST['type']):0;
		
    	$records = $this->getDao('common','commonDict')->getJoinList("type =".$type,$page,$num);
    	
    	return $this->renderJson($records);
    }

    /**
     * http post
     */
    public function create($data)
    {
    	//print_r($_POST);
        return new JsonModel(array('status' => 'success'));

    }

    /**
     * http put
     */
    public function update($id, $data)
    {
    	//echo $id;
    	//print_r($data);
        return new JsonModel(array('status' => 'success'));

    }

    /**
     * http delete
     */
    public function delete($id)
    {
    	//echo $id;
        $raw_post_data = file_get_contents('php://input', 'r');
    	//$method = $_SERVER['REQUEST_METHOD'];	//delete
    	
    	
        return new JsonModel(array('status' => 'success'));

    }

}
