<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
* 设置模型
*/
class Config extends Base{
	
	protected $type = array(
		'id'  => 'integer',
	);

	protected $auto = array('name', 'update_time', 'status'=>1);
	protected $insert = array('create_time');

    protected function setNameAttr($value){
        return strtolower($value);
    }

    protected function getTypeTextAttr($value, $data){
    	$type = config('config_type_list');
    	$type_text = explode(',', $type[$data['type']]);
        return $type_text[0];
    }

	public function lists(){
		$map    = array('status' => 1);
		$data   = $this->where($map)->field('type,name,value')->select()->toArray();
		$config = array();
		if($data && is_array($data)){
			foreach ($data as $value) {
				$config[$value['name']] = $this->parse($value['type'], $value['value']);
			}
		}
		return $config;
	}

	/**
	 * 根据配置类型解析配置
	 * @param  integer $type  配置类型
	 * @param  string  $value 配置值
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	private function parse($type, $value){
		switch ($type) {
			case 'textarea': //解析数组
			$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
			if(strpos($value,':')){
				$value  = array();
				foreach ($array as $val) {
					$list = explode(':', $val);
					if(isset($list[2])){
						$value[$list[0]]   = $list[1].','.$list[2];
					}else{
						$value[$list[0]]   = $list[1];
					}
				}
			}else{
				$value =    $array;
			}
			break;
		}
		return $value;
	}
}