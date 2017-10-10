<?php
namespace Platform\Controller;

use CL\Controller\BaseActionController;
use Zend\View\Model\JsonModel;

class IndexController extends BaseActionController
{
    public function indexAction()
    {
    	$request=$this->getRequest()->getQuery()->toArray();

        $moduleData=$this->getDao('platform','module')->getList('is_delete=0',1,100,'module asc');
    	$module=array(''=>'请选择');
    	foreach($moduleData as $item){
    		$module[$item['id']]=$item['module'];
    	}
    	$currentApi=array();
    	$api=array(''=>'请选择');
    	if(isset($request['module'])&& !empty($request['module'])){
    		$apiData=$this->getDao('platform','api')->getList('is_delete=0 and  module_id='.$request['module'],1,100,'name asc');
    		if(!empty($apiData)){

    			foreach ($apiData as $item){
    				if(isset($request['api']) && !empty($request['api'])){
    					if($request['api']==$item['id'])
    						$currentApi=$item;
    				}

    				$api[$item['id']]=$item['name'];
    			}
    		}
    	}
    	else{
    		$request['module']='';

    	}
		$param=array();
    	if(isset($request['api']) && !empty($request['api'])){

    		$param=$this->getDao('platform','param')->getList('is_delete=0 and api_id='.$request['api'],1,100,'name asc');

    	}
    	else{
    		$request['api']='';
    	}

        if(isset($_COOKIE['apihost'])){
            $apihost = $_COOKIE['apihost'];
        }
        else
    	    $apihost = $this->getServiceLocator()->get('cl_config')['params']['api_domain'];

        if($_COOKIE['platform']){
            $platform = $_COOKIE['platform'];
        }
        else{
            $platform = 'test';
        }
        if($_COOKIE['op']){
            $op = $_COOKIE['op'];
        }
        else{
            $op = 0;
        }

        return array('request'=>$request,'module'=>$module,'api'=>$api,'param'=>$param,'currentApi'=>$currentApi,'apihost'=>$apihost,'op'=>$op,'platform'=>$platform);
    }

    public function changeAction(){
        $post = $this->request->getPost()->toArray();
        if(!empty($post['apihost'])){
            setcookie('apihost',$post['apihost']);
        }
        if(isset($post['op'])){
            setcookie('op',intval($post['op']));
        }

        if(!empty($post['platform'])){
            setcookie('platform',$post['platform']);
        }

        return new JsonModel(array('status'=>0));
    }


    public function manageAction()
    {
    	$request=$this->getRequest()->getQuery()->toArray();
    	if(!isset($request['page']))
    		$request['page']=1;

    	$data=$this->getDao('platform','module')->getList('is_delete=0',$request['page'],100,'module asc');
    	$num=$this->getDao('platform','module')->getCnt('is_delete=0');

        return array('data'=>$data,'num'=>$num,'request'=>$request);
    }

    public function clearCache(){
        $r = $this->memcache() -> flush();
        if($r){
            return new JsonModel(array('status'=> "一键清除缓存成功"));
        }else{
            return new JsonModel(array('status'=>"一键清除缓存失败"));
        }
    }


    public function createmoduleAction(){
    	$post=$this->getRequest()->getPost()->toArray();

    	if(empty($post['id'])){
    		unset($post['id']);
	    	$post=array_merge($post,array('ctime'=>time()));
	    	$this->getDao('platform','module')->insert($post);
    	}
    	else{
    		$id=$post['id'];
    		unset($post['id']);
    		$this->getDao('platform','module')->update($post,'id='.$id);
    	}
    	return new JsonModel(array('status'=>0));
    }



    public function deletemoduleAction(){
    	$post=$this->getRequest()->getPost()->toArray();
    	 if(!empty($post['id']))
    	$this->getDao('platform','module')->delete('id='.$post['id']);
    	return new JsonModel(array('status'=>0));
    }


    public function apiAction(){
    	$request=$this->getRequest()->getQuery()->toArray();
    	$moduleId=$request['moduleId'];

    	if(!isset($request['page']))
            $request['page']=1;
        $cond='is_delete=0 and module_id='.$moduleId;
    	$data=$this->getDao('platform','api')->getList($cond,$request['page'],100,'name desc');
    	$num=$this->getDao('platform','api')->getCnt($cond);

    	$module=$this->getDao('platform','module')->getOne('id='.$moduleId);
    	return array('data'=>$data,'num'=>$num,'request'=>$request,'module'=>$module);
    }

    public function consoleAction(){
        $request=$this->getRequest()->getQuery()->toArray();
        $isQueue=$request['type']?$request['type']:0;


        $cond='is_delete=0 and is_queue='.$isQueue;
        $data=$this->getDao('platform','console')->getList($cond,1,100,'ctime desc');
        $num=$this->getDao('platform','console')->getCnt($cond);

        return array('data'=>$data,'num'=>$num,'request'=>$request,'type'=>$isQueue);
    }

    public function consolelogAction()
    {
        $request=$this->getRequest()->getQuery()->toArray();
        if(!isset($request['page']))
            $request['page']=1;

        $cond = array();
        if(isset($request['controller']))
           $cond['controller'] =  $request['controller'];

        if(isset($request['action']))
            $cond['action'] =  $request['action'];

        $cond['offset'] = ($request['page']-1)*20;
        $cond['limit'] = 20;

        $data=$this->getDao('platform','consoleLog')->getLogList($cond);

        $data['request'] = $request;
        return $data;
    }


    public function checkconsoleAction(){
        $post=$this->getRequest()->getQuery()->toArray();
        $status =0;

        if(!empty($post['name'])){
            $client= $this->getGearman();
// $r = $client->doNormal("runcrontab", '*/1 * * * *,test_queue');
            if($post['is_queue']){
                $comand='check';
            }
            else{
                $comand = 'checkcrontab';
            }

            $status = $client->doNormal($comand, $post['name']);
        }

        return new JsonModel(array('status'=>$status));
    }


    public function startconsoleAction(){
        $post=$this->getRequest()->getPost()->toArray();
        $status =0;

        if(!empty($post['name'])){
            $client= $this->getGearman();
// $r = $client->doNormal("runcrontab", '*/1 * * * *,test_queue');
            if($post['is_queue']){
                $comand='run';
            }
            else{
                $comand = 'runcrontab';
            }

            $status = $client->doNormal($comand, $post['name']);
        }

        return new JsonModel(array('status'=>$status));
    }

    public function stopconsoleAction(){
        $post=$this->getRequest()->getPost()->toArray();
        $status =0;

        if(!empty($post['name'])){
            $client= $this->getGearman();
// $r = $client->doNormal("runcrontab", '*/1 * * * *,test_queue');
            if($post['is_queue']){
                $comand='kill';
            }
            else{
                $comand = 'killcrontab';
            }

            $status = $client->doNormal($comand, $post['name']);
        }

        return new JsonModel(array('status'=>$status));
    }

    public function createconsoleAction(){
        $post=$this->getRequest()->getPost()->toArray();

        if(empty($post['id'])){
            unset($post['id']);
            $post=array_merge($post,array('ctime'=>time()));
            $this->getDao('platform','console')->insert($post);
        }
        else{
            $id=$post['id'];
            unset($post['id']);
            $this->getDao('platform','console')->update($post,'id='.$id);
        }
        return new JsonModel(array('status'=>0));
    }

    public function deleteconsoleAction(){
        $post=$this->getRequest()->getPost()->toArray();
        if(!empty($post['id']))
            $this->getDao('platform','console')->delete('id='.$post['id']);
        return new JsonModel(array('status'=>0));
    }

    public function createapiAction(){
    	$post=$this->getRequest()->getPost()->toArray();

    	if(empty($post['id'])){
    		unset($post['id']);
    		$post=array_merge($post,array('ctime'=>time()));
    		$this->getDao('platform','api')->insert($post);
    	}
    	else{
    		$id=$post['id'];
    		unset($post['id']);
    		$this->getDao('platform','api')->update($post,'id='.$id);
    	}
    	return new JsonModel(array('status'=>0));
    }

    public function deleteapiAction(){
    	$post=$this->getRequest()->getPost()->toArray();
    	if(!empty($post['id']))
    		$this->getDao('platform','api')->delete('id='.$post['id']);
    	return new JsonModel(array('status'=>0));
    }

    public function paramAction(){
    	$request=$this->getRequest()->getQuery()->toArray();
    	$apiId=$request['apiId'];

    	if(!isset($request['page']))
    		$request['page']=1;
    	$cond='is_delete=0 and api_id='.$apiId;
    	$data=$this->getDao('platform','param')->getList($cond,$request['page'],100,'name asc');
    	$num=$this->getDao('platform','param')->getCnt($cond);

    	$api=$this->getDao('platform','api')->getOne('id='.$apiId);
    	return array('data'=>$data,'num'=>$num,'request'=>$request,'api'=>$api);
    }

    public function createparamAction(){
    	$post=$this->getRequest()->getPost()->toArray();
    	$post['name'] = trim($post['name']);
    	if(empty($post['id'])){
    		unset($post['id']);
    		$post=array_merge($post,array('ctime'=>time()));
    		$this->getDao('platform','param')->insert($post);
    	}
    	else{
    		$id=$post['id'];
    		unset($post['id']);
    		$this->getDao('platform','param')->update($post,'id='.$id);
    	}
    	return new JsonModel(array('status'=>0));
    }

    public function deleteparamAction(){
    	$post=$this->getRequest()->getPost()->toArray();
    	if(!empty($post['id']))
    		$this->getDao('platform','param')->delete('id='.$post['id']);
    	return new JsonModel(array('status'=>0));
    }

    public function testAction(){
    	$re=$this->getRequest()->getPost()->toArray();
    	$param=array();
    	if(!empty($re['param'])){
    	foreach($re['param'] as $key=>$value){
    		$pos = strrpos($re['api_route'], '/'.$key);
			if ($pos  && (strrpos($key,':')!==false))
    			$re['api_route']=str_replace('/'.$key,'/'.$value,$re['api_route']);
			else
    			{
        			$param[$key]=$value;
    			}
    	}
    	}

        $platform = 'test';
        if(!empty($_COOKIE['platform'])){
            $platform = $_COOKIE['platform'];
        }

        $op='';
        if(!empty($_COOKIE['op'])){
            $op = $_COOKIE['op'];
        }


        $param = array_merge($param,array('request_host'=>$platform,'request_user'=>$op));

    	 $url = sprintf("%s?%s", $re['api_route'], http_build_query($param));
    	$info = $this->callAPI($re['request_type'],$re['api_route'],$param);//put数据
        $apiTime = '';
        $sql = array();
        $mongo = array();
		$solr = array();
        $value = json_decode($info,true);

        if(is_array($value)){
        if(!empty($value['analysis'])){
                $apiTime = $value['analysis']['api_consume'];
                if(is_array( $value['analysis']['sql_analysis'])){
                    foreach($value['analysis']['sql_analysis'] as $item2){
                        if(!empty($item2['read'])){
                            $sql = array_merge($sql,$item2['read']);
                            $sql = array_merge($sql,$item2['write']);
                        }
                    }
                }

                if(is_array( $value['analysis']['mongo_analysis'])){
                    foreach($value['analysis']['mongo_analysis'] as $item2){
                        if(!empty($item2['read'])){
                            $mongo = array_merge($mongo,$item2['read']);
                            if(isset($item2['write']))
                            $mongo = array_merge($mongo,$item2['write']);
                        }
                    }
                }

				if(is_array( $value['analysis']['solr_analysis'])){
					//print_r($value['analysis']['solr_analysis']);exit;
                    foreach($value['analysis']['solr_analysis'] as $key=> $item2){
                        if(!empty($item2)){
							foreach($item2 as $type => $nv){
								foreach ($nv as  $vvv) {
									$solr[] = array('type' => $key, 'uri' => $vvv['uri'], 'consume' => $vvv[consume],'method' => $vvv['type']);
								}
							}
                        }
                    }
                }
        }

        $sqlStr = '';
        $sqlSum=0;
        if(!empty($sql)){
            foreach($sql as $item){
                $sqlStr .= '<p>'.$item['sql'].'  | <span><b>耗时</b>'.$item['consume'].'</span></p>';
                $sqlSum += $item['consume'];
            }
        }

        $mongoStr = '';
        $mongoSum=0;
        if(!empty($mongo)){
            foreach($mongo as $item){
                $mongoStr .= '<p>'.print_r($item['sql'],true).'  | <span><b>耗时</b>'.$item['consume'].'</span></p>';
                $mongoSum+=$item['consume'];
            }
        }

		$solrStr = '';
		$solrSum = 0;
		if(!empty($solr)){
			foreach($solr as $item){
				$solrStr .='<p><span><b>'.$item['type'].'</b></span> | '.urldecode($item['uri']).'  | <span><b>耗时</b>'.$item['consume'].'</span></p>';
				$solrSum += $item['consume'];
			}
		}
        unset($value['analysis']);

        $info = json_encode(($value));
        }

        if(isset($_COOKIE['apihost'])){
            $apihost = $_COOKIE['apihost'];
        }
        else
            $apihost = $this->getServiceLocator()->get('cl_config')['params']['api_domain'];

        $url =$apihost.$url;

    	return @new JsonModel(array('status'=>0,'data'=>$info,'url'=>$url,'apiTime'=>$apiTime,'sql'=>''.$sqlStr.'','mongoTime'=>$mongoSum,'solrTime'=>$solrSum,'sqlTime'=>$sqlSum,'solr' => $solrStr, 'mongo'=>''.$mongoStr.'', 'json'=>'<pre>' . print_r(json_decode($info,true),true).'</pre>'));
    }

    function callAPI($method, $url, $data = false)
    {
        //echo 'funk'.$method; echo $url; print_r($data);
    	return $this->getDao('platform','param')->callapi(strtoupper($method), $url, $data);
    }

    function cacheAction(){
        $request=$this->getRequest()->getQuery()->toArray();

        if(!isset($request['page']))
            $request['page']=1;

        if(!isset($request['cache_time']))
            $cacheTime = '';
        else {
            $cacheTime=$request['cache_time'];
        }

        if(!isset($request['is_list_cache']))
            $isListCache = 0;
        else {
            $isListCache=$request['is_list_cache'];
        }

        $cond='is_delete=0 and is_cache=1  and is_list_cache = '.$isListCache;
        if($cacheTime!==''){
            $cond .=' and cache_time='.$cacheTime;
        }

        $data=$this->getDao('platform','api')->getList($cond,$request['page'],100,'route desc');
        $num=$this->getDao('platform','api')->getCnt($cond);

        return array('data'=>$data,'num'=>$num,'request'=>$request,'param'=>array('cache_time'=>$cacheTime,'is_list_cache'=>$isListCache));
    }

    function deletecacheAction(){
        $post=$this->getRequest()->getPost()->toArray();
        $msg = '';
        if(empty($post['name']))
            $msg = '名称为空';
        if($msg){
            return new JsonModel(array('status'=>1,'errmsg'=>$msg));
        }


//        echo $post['name'];
//        $d = $this->getMemcache()->get($post['name']);

        $result = $this->getMemcache()->delete($post['name']);
//        var_dump($result);
//        $d = $this->getMemcache()->get($post['name']);
//        var_dump($d);
        if(empty($result))
            return new JsonModel(array('status'=>1,'errmsg'=>'缓存清除失败'));
        return new JsonModel(array('status'=>0));

    }

    //API调用报表
    function staticAction(){

        $request=$this->getRequest()->getQuery()->toArray();
        if(!isset($request['page'])){
            $page  = 1;
        }
        else{
            $page = intval($request['page']);
        }
        $offset = ($page-1)*10;
        $limit = 10;

        if(isset($request['type']) && !empty($request['type'])){
            $type = $request['type'];
        }
        else{
            $type = 'slow';
        }

        if(isset($request['method']) && !empty($request['method'])){
            $method = $request['method'];
        }
        else{
            $method = 'get';
        }

        if(isset($request['period']) && !empty($request['period'])){
            $period = $request['period'];
        }
        else{
            $period = 0;
        }

        $param = array('offset'=>$offset,'limit'=>$limit,'request_method'=>$method ,'type'=>$type,'period'=>$period);
        $r = $this->getDao('platform', 'apiLog')->getStaticList($param);
        return  array('data'=>$r['list'],'page'=>$page,'num'=>100,'static_num'=>$r['num']);
    }

    //单个API被调用情况
    function staticdetailAction(){

        $request=$this->getRequest()->getQuery()->toArray();
        if(!isset($request['route']) || empty($request['route'])){
            exit('未指定具体API');
        }

        if(!isset($request['page'])){
            $page  = 1;
        }
        else{
            $page = intval($request['page']);
        }

        $limit = 20;
        $offset = ($page-1)*$limit;

        if(isset($request['method']) && !empty($request['method'])){
            $method = $request['method'];
        }
        else{
            $method = 'get';
        }

        if(isset($request['period']) && !empty($request['period'])){
            $period = $request['period'];
        }
        else{
            $period = 0;
        }

        $moduleType=array(
            'get' => '0',
            'post' => '1',
            'put' => '2',
            'delete' => '3'
        );

        $apiData=$this->getDao('platform','api')->getList('is_delete=0 and  route="'.$request['route'].'" and type = '.$moduleType[$method] ,0,1);

        if(empty($apiData)){
            exit('无此api');
        }
        $apiData = $apiData[0];

        $param = array('offset'=>$offset,'limit'=>$limit,'request_method'=>$method ,'request_api'=>$request['route'],'period'=>$period);
        $r = $this->getDao('platform', 'apiLog')->getDetailList($param);
        //print_r($r);
        return  array('data'=>$r,'page'=>$page,'apiData'=>$apiData);

    }

    function getparamAction(){
        $request=$this->getRequest()->getQuery()->toArray();
        if(!isset($request['route']) || empty($request['route'])){
            exit('未指定具体API');
        }

        if(isset($request['method']) && !empty($request['method'])){
            $method = $request['method'];
        }
        else{
            $method = 'get';
        }
        $param = array('offset'=>0,'limit'=>1,'request_method'=>$method ,'request_api'=>$request['route'],'period'=>2);
        $r = $this->getDao('platform', 'apiLog')->getDetailList($param);
        $data='';
        if(!empty($r['list'])){
            $data = $r['list'][0]['request_params'];
        }
        return new JsonModel(array('data'=>$data));

    }

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

    function alltestAction(){

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
    Header("WWW-Authenticate: Basic realm=\" Login\"");
    Header("HTTP/1.0 401 Unauthorized");

    echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
    exit;
}

        $start = $this->getMicrotime();
       echo <<<EOF
       <!DOCTYPE html><html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Le styles -->
        <link href="/assets/css/jquery.fancybox.css" media="screen" rel="stylesheet" type="text/css">
<link href="/assets/img/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
<link href="/assets/bootstrap/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/assets/bootstrap/css/bootstrap-theme.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/assets/css/style.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/assets/jquery/jquery.min.js"></script>
        <!-- Scripts -->
        <script type="text/javascript" src="/assets/jquery/jquery.fancybox.js"></script>
<!--[if lt IE 9]><script type="text/javascript" src="/assets/html5shiv/html5shiv.min.js"></script><![endif]-->
<!--[if lt IE 9]><script type="text/javascript" src="/assets/respond/respond.min.js"></script><![endif]-->

<script type="text/javascript" src="/assets/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
        <table border=1 width="100%">
        <tr>
              <td width ="3%">序号</td>
              <td width="20%">API名称</td>
               <td width=22%">API URL</td>
               <td width=5%">请求方式</td>
              <td width="40%">请求参数</td>
               <td width="5%">返回结果</td>
                   <td width="5%">耗时</td>
                            </tr>
EOF;

        ini_set("max_execution_time", "1800");
        $cond='is_delete=0';
        $data=$this->getDao('platform','api')->getList($cond,0,10000,'module_id desc ,name desc');

        $moduleType=array(
            '0' => 'get',
            '1' => 'post',
            '2' => 'put',
            '3' => 'delete'
        );
        $i=1;
        foreach($data as $item){

			$out =  '<tr><td>'.$i.'</td><td><a target="_blank" href="/platform/index/index?module='.$item['module_id'].'&api='.$item['id'].'">'.$item['name'].'</a></td><td>'.$item['route'].'</td><td>'.$moduleType[$item['type']].'</td>';
            $i++;
            if(!in_array($item['type'],array(0,2))){
                $out .='<td>post,delete类型暂不测试</td><td></td><td></td>';
                $out . '</tr>';
                echo $out;
                continue;
            }


            $param = array('offset'=>0,'limit'=>1,'request_method'=>$moduleType[$item['type']] ,'request_api'=>$item['route'],'period'=>2);
            $r = $this->getDao('platform', 'apiLog')->getDetailList($param);
            $param=array();
            if(!empty($r['list'])){
                $re = $r['list'][0];
                $request_params = $re['request_params'];

                if(!empty($request_params )){
                    foreach($request_params  as $key=>$value){
                        $pos = strrpos($re['request_api'], '/'.$key);
                        if ($pos  && (strrpos($key,':')!==false))
                            $re['request_api']=str_replace('/'.$key,'/'.$value,$re['request_api']);
                        else
                        {
                            $param[$key]=$value;
                        }
                    }
                }

                //echo $url = sprintf("%s?%s", $re['request_api'], http_build_query($param));
               //exit;
                $info = $this->callAPI($moduleType[$item['type']],$re['request_api'],$param);
                $value = json_decode($info,true);
                $title = '测试成功';
                $consume ='暂无';
                if(empty($value)){
                    $title = '<red>测试失败</red>';
                    $value = '[API ERROR]'.$re['request_api']."\n".$info;
                }
                else{
                    $consume = $value['analysis']['api_consume'];
                    $value = print_r($value,true);
                }

                $out .= '<td><pre>'.print_r($param,true).'</pre></td>';

                $out .= '<td><a class="fancybox" href="#error_in'.$item['id'].'">'.$title.'</a></td>';
                $out .=    '<div id="error_in'.$item['id'].'" style="width:700px;display: none;">';
                $out .=  '<code><pre>'.htmlspecialchars($value).'</pre></code></div>';
                $out .= '<td>'.$consume.'</td>';

            }
            else{
                $out .='<td>暂无测试数据</td><td></td><td></td>';
            }

            $out . '</tr>';
            echo $out;


        }
        echo '</table></body>';
        echo <<<EOF
<script>
    $(document).ready(function(){
            $('.fancybox').fancybox();
            alert('测试完毕,共耗时
EOF;
        echo round($this->getMicrotime()-$start,2);
echo <<<EOF
');
        });
</script>

EOF;


        echo '</html>';
        exit;
    }

    private function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    function infoAction(){
        phpinfo();exit;
    }

}
