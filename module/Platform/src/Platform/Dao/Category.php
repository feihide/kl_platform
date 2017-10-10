<?php
namespace Platform\Dao;

use CL\Dao\BaseDao;
use Zend\Db\Sql\Sql;
class  Category extends BaseDao
{
	public function getListByParent($id,$offset,$limit){
	    return $this->api('GET', '/api/common/dicts',array('conditions'=>'parent_id='.$id,'skip'=>$offset,'limit'=>$limit));
	}
	
	public function getCat($id){
	    return $this->api('GET', '/api/common/dicts/'.$id);
	}
	
	public function createCat($data){
	    return $this->api('POST','/api/common/dicts',$data);
	}
	
	public function updateCat($data,$id){
	    return $this->api('PUT','/api/common/dicts/'.$id,$data);
	}
	
	public function deleteCat($id){
	    return $this->api('DELETE','/api/common/dicts/'.$id);
	}
	
}
