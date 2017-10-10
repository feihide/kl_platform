<?php
namespace Platform\Controller;

use CL\Controller\BaseActionController;
use Zend\View\Model\JsonModel;

class CategoryController extends BaseActionController
{
    public function indexAction()
    {	
    	$request=$this->getRequest()->getQuery()->toArray();
    	
    	
    	if(!isset($request['parentId']) or empty($request['parentId'])){
    	    $request['parentId']=0;
    	}
        
    	$child=$this->getDao('platform','category')->getListByParent($request['parentId'],0,100);
    	
        if($request['parentId']){
    	   $parent=$this->getDao('platform','category')->getCat($request['parentId']);
        }
        else{
            $parent=array();
        }
        return array('request'=>$request,'parent'=>$parent,'data'=>$child['list'],'num'=>$child['num']);
    }
    
    public function createAction(){
    	$post=$this->getRequest()->getPost()->toArray();
    	
    	if(empty($post['id'])){
    		unset($post['id']);
    		if(empty($post['sort']))
    		     unset($post['sort']);
	    	$r=$this->getDao('platform','category')->createCat($post);
    	}
    	else{
    		$id=$post['id'];
    		unset($post['id']);
    		$r=$this->getDao('platform','category')->updateCat($post,$id);
    	}
    	return new JsonModel(array('status'=>0));
    }
    
    public function deleteAction(){
        $post=$this->getRequest()->getPost()->toArray();
    	 if(!empty($post['id']))
    	$this->getDao('platform','category')->deleteCat($post['id']);
    	return new JsonModel(array('status'=>0));
    }
    
    public function treeAction(){
        return array();
    }
    

    public function getTreeAction(){
        $request=$this->getRequest()->getQuery()->toArray();
        if(($request['root']=='source')){
            $request['root']=0;
            $root=array("text"=>"分类列表", "expanded"=>true, "classes"=>"important");
        }
        
        $child=$this->getDao('platform','category')->getListByParent($request['root'],0,100);
        $list=array();
        if(!empty($child['list'])){
            foreach($child['list'] as $item){
                $tmp=array();
                $tmp["text"] ="[ {$item['id']} ]". $item['name'].'['.$item['code'].']';
                $tmp["id"]= $item['id']; 
                $tmp["hasChildren"]= true;
                $list[]=$tmp;
            }
        }
        
        if($request['root']==0){
            $root['children']=$list;
            $root=array($root);
        }
        else
            $root = $list;
        
        return new JsonModel($root);
    }
    
}
