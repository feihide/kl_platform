<?php
/**
 * 品牌controller
 * spring@2013-11-25 
 */

namespace Common\Controller;

use CL\Controller\CommonBaseController;

class BrandController extends CommonBaseController
{
    /**
     * 获取某个$id数据
     */
    public function get($id)
    {    	
    	$category = isset($_REQUEST['category'])?intval($_REQUEST['category']):0;
    	 
    	$record = $this->getDao('common','commonBrand')->getChoiceOne($id,$category);
    	
    	return $this->renderJson($record);
    }
    
    
    /**
     * 获取列表数据
     */
    public function getList()
    {
    	$page = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
    	$num = isset($_REQUEST['num'])?intval($_REQUEST['num']):0;
    
    	$category = isset($_REQUEST['category'])?intval($_REQUEST['category']):0;
    	$records = $this->getDao('common','commonBrand')->getAllList($category,$page,$num);
    	
    	return $this->renderJson($records);
    }

    /**
     * http post
     */
    public function create($data)
    {
        return new JsonModel(array('status' => 'success'));

    }

    /**
     * http put
     */
    public function update($id, $data)
    {
        return new JsonModel(array('status' => 'success'));

    }

    /**
     * http delete
     */
    public function delete($id)
    {
        return new JsonModel(array('status' => 'success'));

    }
}
