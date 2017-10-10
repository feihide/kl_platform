<?php
/**
 * Created by PhpStorm.
 * User: zhangyifei
 * Date: 15/8/13
 * Time: 上午10:48
 */
namespace Platform\Controller;

use CL\Controller\BaseActionController;
use Zend\View\Model\JsonModel;

class IndexController extends BaseActionController{

    //单个API被调用情况
    function errorAction(){

        $request=$this->getRequest()->getQuery()->toArray();

        if(!isset($request['page'])){
            $page  = 1;
        }

        $limit = 20;
        $offset = ($page-1)*$limit;
        if(isset($request['method']) && !empty($request['method'])){
            $method = $request['method'];
        }
        else{
            $method = '';
        }

        if(isset($request['period']) && !empty($request['period'])){
            $period = $request['period'];
        }
        else{
            $period = 0;
        }


        $param = array('offset'=>$offset,'limit'=>$limit,'period'=>$period);
        if($method){
            $param['request_method']=$method;
        }
        $r = $this->getDao('platform', 'apiLog')->getErrorList($param);

        return  array('data'=>$r,'page'=>$page);

    }
}