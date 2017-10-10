<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
<body>
<?php
error_reporting(0);

echo '<form action=""  method="get">';
echo '<div>url:<input name="url" size="200"  value="'.$_GET['url'].'"/></div>';
echo	'<div >id:<input name="id" value="'.$_GET['id'].'"/></div>';
echo '<div>params:write querystring<textarea cols="100"  rows="5"  name="params">'.(isset($_GET['params'])?$_GET['params']:'').'</textarea></div>';
echo '<select name="type">';
$t=array('GET','POST','PUT','DELETE');
foreach($t as $item){
	echo '<option value="'.$item.'"';
	if(isset($_GET['type'])  && $_GET['type']==$item) echo ' selected ';		
	echo  '>'.$item.'</option>';
}
echo '<input type="submit" value="OK" />';
echo '</form>';
// Method: POST, PUT, GET etc

function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

   // Optional Authentication:
  //  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  //  curl_setopt($curl, CURLOPT_USERPWD, "username:password");
	
  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    switch ($method)
    {	
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":		
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($data));
            break;
	  case "DELETE":		
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

		break;
        default://GET
            if ($data)
          	$url = sprintf("%s?%s", $url, http_build_query($data));	
			
    }
	echo 'request URL: '.$url.'<br/>';
	  curl_setopt($curl, CURLOPT_URL, $url);

	 $result = curl_exec($curl);
	 curl_close($curl);
    return $result;
}


//1、通过file_get_contents()调用	只有GET
//$info = json_decode(file_get_contents("http://www.cailangapi.dev/users/"));//获取所有
//$info = json_decode(file_get_contents("http://www.cailangapi.dev/users/2"));//获取某个


//2、使用curl访问RESTful

$data = array(
	"id" => 3
);
//$info = CallAPI("","http://www.cailangapi.dev/users/");//获取所有
//$info = CallAPI("","http://www.cailangapi.dev/users/",$data);//获取某个

$m=isset($_GET['type'])?$_GET['type']:'GET';
$url=isset($_GET['url'])?$_GET['url']:'';
if(isset($_GET['id'])){
	$url.=$_GET['id'];
}

echo '-------------------------RESULT-------------------------------------<br/>';
if($url){
	$data=array();
	if(isset($_GET['params']) && !empty($_GET['params'])){
		
		$p=explode('&',$_GET['params']);
		foreach($p as $item){
			if(!empty($item)){
			$pp=explode('=',$item);
			$data[$pp[0]]=$pp[1];
			}
		}
	}

//$info = json_decode(CallAPI("POST","http://www.cailangapi.dev/users",$data));//post数据

$info = CallAPI($m,urldecode($url),$data);//put数据
print_r($info);
//$info = CallAPI("DELETE","http://www.cailangapi.dev/users/2",$data);//delete数据
}
else{
	echo '请输入url';
}
?>
</body>
</html>