<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id: UsersController.php 30 2013-11-20 07:44:03Z quanwei $
 */

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class PassportController extends AbstractRestfulController
{
	private $adapter;
	
	public function __construct(){
		
	}
    /**
     * http get
     */
    public function get($id)
    {
    	echo 'ff';exit;
    	
        return new JsonModel(array('id' => $id, 'nickname' => 'ethan', 'email' => 'quanwei@cailang.com'));
    }


    /**
     * http get list
     */
    public function getList()
    {
        return new JsonModel(array('ethan', 'kop'));

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
    	$raw_post_data = file_get_contents('php://input', 'r');print_r($raw_post_data);exit;
    	//$method = $_SERVER['REQUEST_METHOD'];	//delete
    	
    	
        return new JsonModel(array('status' => 'success'));

    }

}
