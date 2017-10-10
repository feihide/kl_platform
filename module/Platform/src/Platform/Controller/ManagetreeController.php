<?php
namespace Platform\Controller;

use CL\Controller\BaseActionController;
use Zend\View\Model\JsonModel;

class ManagetreeController extends BaseActionController
{
    public function indexAction()
    {	
    	$request=$this->getRequest()->getQuery()->toArray();
 	    $parentId=$request['parentId'];
        if($parentId == ''){
            $parentId = 0;
        }
        
        $cond=isset($parentId)?'is_delete=0 and parent_id='.$parentId:'is_delete = 0';

        if(!isset($request['page']))
    		$request['page']=1;
            
        // 每页显示条数
        $limit = 20;        
    	$data=$this->getDao('platform','manageTree')->getList($cond,$request['page'],$limit,'order asc');
        $num=$this->getDao('platform','manageTree')->getCnt($cond);
		
		$data = $this->getDao('platform','manageTree')->getList($cond,$request['page'],$limit,'order asc');
       
        $tree=$this->getDao('platform','manageTree')->getOne('id='.$parentId);
        return array('data'=>$data,'num'=>$num,'request'=>$request,'tree'=>$tree,'limit'=>$limit);
    }
    

    
    //添加编辑机构
    public function createTreeAction(){
        
    	$post=$this->getRequest()->getPost()->toArray();
        

    	if( strstr($_REQUEST['parent_id'],'ins') != false){
            
            $post['parent_id'] = substr($_REQUEST['parent_id'],3);
            $post['order'] = $post['order']==''?0:$post['order'];
	    	$post=array_merge($post,array('ctime'=>time()));
	    	$this->getDao('platform','manageTree')->insert($post);
    	}
    	else{
    		$id=$post['parent_id'];
    		unset($post['parent_id']);
            $post=array_merge($post,array('utime'=>time()));
    		$this->getDao('platform','manageTree')->update($post,'id='.$id);
    	}
    	return new JsonModel(array('status'=>0));
    }

    // 临时删除机构
    public function deleteTreeAction(){
        $post=$this->getRequest()->getPost()->toArray();
    	$request=$this->getRequest()->getQuery()->toArray();
        $num=$this->getDao('platform','manageTree')->getCnt("parent_id=$post[id] and is_delete = 0");
        
        if($num != 0){
            return new JsonModel(array('status'=>500));
        }else{
       	    if(!empty($post['id']))
        	$this->getDao('platform','manageTree')->delete('id='.$post['id']);
        	
            return new JsonModel(array('status'=>0));
        }
    }

    
    
    
}
