<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
* 设置模型
*/
class Base extends \think\Validate{


	protected function requireIn($value, $rule, $data){
		if (is_string($rule)) {
			$rule = explode(',', $rule);
		}else{
			return true;
		}
		$field = array_shift($rule);
		$val = $this->getDataValue($data, $field);
		if (!in_array($val, $rule) && $value == '') {
			return false;
		} else {
			return true;
		}
	}
}