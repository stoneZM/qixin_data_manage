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
 * 分类模型
 */
class ActionLog extends Base{

	protected function getModelIdAttr($value, $data){
		$value = get_document_field($data['model'], "name", "id");
		return $value ? $value : 0;
	}
}