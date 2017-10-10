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

class DebugController extends BaseActionController{

    //单个API被调用情况
    function indexAction(){

        $request=$this->getRequest()->getQuery()->toArray();

        if(!isset($request['page'])){
            $page  = 1;
        }else{
            $page = $request['page'];
        }

        $limit = 20;
        $offset = ($page-1)*$limit;

        if(isset($request['env']) && !empty($request['env'])){
            $env = $request['env'];
        }
        else{
            $env = '';
        }
        if(isset($request['city']) && !empty($request['city'])){
            $city = $request['city'];
        }
        else{
            $city = '';
        }
        if(isset($request['module']) && !empty($request['module'])){
            $module = $request['module'];
        }
        else{
            $module = '';
        }

        if(isset($request['type']) && !empty($request['type'])){
            $type = $request['type'];
        }
        else{
            $type = '';
        }


        $param = array();
        if($env){
            $param['env']=$env;
        }
        if($type){
            $param['type']=$type;
        }
        if($city){
            $param['city']=$city;
        }
        if($module){
            $param['platform']=$module;
        }

        $list = $this->getDao('platform', 'debug')->getLogList($param,$offset,$limit);
        $num = $this->getDao('platform', 'debug')->getLogNum($param);

        return  array('list'=>$list,'num'=>$num,'page'=>$page);

    }

    public function deleteAction(){
        $post=$this->getRequest()->getPost()->toArray();
        if(!empty($post['id'])){
            $oid= new \MongoId($post['id']);
            $this->getDao('platform','debug')->delete(array('_id'=>$oid));
        }
        return new JsonModel(array('status'=>0));
    }

    public function delallAction(){
        $request=$this->getRequest()->getQuery()->toArray();

        if(isset($request['env']) && !empty($request['env'])){
            $env = $request['env'];
        }
        else{
            $env = '';
        }
        if(isset($request['city']) && !empty($request['city'])){
            $city = $request['city'];
        }
        else{
            $city = '';
        }
        if(isset($request['module']) && !empty($request['module'])){
            $module = $request['module'];
        }
        else{
            $module = '';
        }

        if(isset($request['type']) && !empty($request['type'])){
            $type = $request['type'];
        }
        else{
            $type = '';
        }


        $param = array();
        if($env){
            $param['env']=$env;
        }
        if($type){
            $param['type']=$type;
        }
        if($city){
            $param['city']=$city;
        }
        if($module){
            $param['platform']=$module;
        }
        $this->getDao('platform','debug')->delete($param);
        return new JsonModel(array('status'=>0));
    }
}