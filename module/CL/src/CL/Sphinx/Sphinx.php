<?php
/**
 * 连接sphinx
 * spring@2013-12-09
 */
namespace CL\Sphinx;
use CL\Sphinx\SphinxClient;

class Sphinx
{
	private $_config;	
	private static  $client = null;
	
    function __construct($conf=null) 
    {	
		if($conf!==null){            
           $this->_config = $conf;  
		   $this->getSearchClient();
        }		
		 	
	}

	
	private function getSearchClient() {	
		if (is_null(self::$client)) {	
			self::$client = new SphinxClient();
			self::$client->SetServer($this->_config['host'], $this->_config['port']);
			self::$client->SetConnectTimeout($this->_config['timeout']);
			self::$client->SetArrayResult(true);
			self::$client->SetMatchMode(SPH_MATCH_ALL);		
		}
	
	}
	
	/**
	 * 查询	
	 * @param string $keywords 搜索关键字
	 * @param string $index 索引名
	 * @param int $num 记录数
	 * @param int $offset 偏移量
	 * @param boolean $isorder 排序
	 * @param string $filter 过滤属性名
	 * @param array $filter 过滤属性整数值数组
	 * @return array
	 */
	public function search ($keywords,$index = '*', $num = 0, $offset = 0,$isorder = false,$filter='' , $filtervals=array())
	{	
		if($num > 0) {
			self::$client->SetLimits($offset, $num);
		}
		if ($isorder) {
			self::$client->SetSortMode(SPH_SORT_EXTENDED, '@weight DESC, len ASC');
		}
		
		if(!empty($filter) && (count($filtervals))) {
			self::$client->SetFilter($filter, $filtervals);
		}
		
		if(!$this->is_utf8($keywords)) {
			$keywords =  mb_convert_encoding($keywords,'UTF-8');
		}
		$result = self::$client->Query($keywords, $index);		
		
		
		//得到想要的数据
		$data = array();
		if ($result['total_found']>0) { //搜到	
			$data = $result['matches'];		
		}
		 
		return array(
				'data' => $data,
				'words' => array_keys($result['words']),
				'num' => $result['total_found']
		);
	}

	/**
	 * 是否utf-8编码
	 */
	private function is_utf8($string)
	{
			return preg_match('%^(?:
			 [\x09\x0A\x0D\x20-\x7E]            # ASCII
		   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
		   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
		   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
		   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
		   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
		   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	   )*$%xs', $string);
	}
   
}