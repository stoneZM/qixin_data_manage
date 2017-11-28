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
* 用户模型
*/
class SeoRule extends Base{

	public $keyList = array(
		array('name'=>'id','title'=>'identifying','type'=>'hidden'),
		array('name'=>'title','title'=>'seo_name','type'=>'text','option'=>'','help'=>''),
		array('name'=>'app','title'=>'seo_module_name','type'=>'select','option'=>array('*'=>'seo_all','index'=>'seo_index','user'=>'seo_user'),'help'=>''),
		array('name'=>'controller','title'=>'controller','type'=>'text','option'=>'','help'=>''),
		array('name'=>'action','title'=>'seo_action','type'=>'text','option'=>'','help'=>''),
		array('name'=>'seo_title','title'=>'seo_title','type'=>'text','option'=>'','help'=>''),
		array('name'=>'seo_keywords','title'=>'seo_keyword','type'=>'text','option'=>'','help'=>''),
		array('name'=>'seo_description','title'=>'seo_description','type'=>'text','option'=>'','help'=>''),
		array('name'=>'status', 'title'=>'state', 'type'=>'select','option'=>array('0'=>'disable','1'=>'enable'),'help'=>''),
		array('name'=>'sort','title'=>'sort','type'=>'text','option'=>'','help'=>'')
	);
	
	
	public function initialize() {
		parent::initialize();
		foreach ($this->keyList as $key => $value) {
			if ($value['name'] == 'status' || $value['name'] == 'app') {	
				foreach ($value['option'] as &$item) {
					$item = lang($item);
				}
			}
			$this->keyList[$key] = $value;
		}
	}
	

	protected function setAppAttr($value){
		return $value ? $value : '*';
	}

	protected function setControllerAttr($value){
		return $value ? $value : '*';
	}

	protected function setActionAttr($value){
		return (isset($value) && $value) ? $value : '*';
	}

	protected function getAppAttr($value){
		return $value ? $value : '*';
	}

	protected function getControllerAttr($value){
		return $value ? $value : '*';
	}

	protected function getActionAttr($value){
		return (isset($value) && $value) ? $value : '*';
	}

	protected function getRuleNameAttr($value, $data){
		return $data['app'].'/'.$data['controller'].'/'.$data['action'];
	}

	public function getMetaOfCurrentPage($seo){
		$request = \think\Request::instance();
		foreach ($seo as $key => $value) {
			if (is_array($value)) {
				$seo_to_str[$key] = implode(',', $value);
			}else{
				$seo_to_str[$key] = $value;
			}
		}
		$result = $this->getMeta($request->module(), $request->controller(), $request->action(), $seo_to_str);
		return $result;
	}

	private function getMeta($module, $controller, $action, $seo){
		//获取相关的规则
		$rules = $this->getRelatedRules($module, $controller, $action);

		//按照排序计算最终结果
		$title = '';
		$keywords = '';
		$description = '';

		$need_seo = 1;
		foreach ($rules as $e) {
			//如果存在完全匹配的seo配置，则不用程序设置的seo资料
			if ($e['app'] && $e['controller'] && $e['action']) {
				$need_seo = 0;
			}
			if (!$title && $e['seo_title']) {
				$title = $e['seo_title'];
			}
			if (!$keywords && $e['seo_keywords']) {
				$keywords = $e['seo_keywords'];
			}
			if (!$description && $e['seo_description']) {
				$description = $e['seo_description'];
			}
		}
		if ($need_seo) { //默认让全站的seo规则优先级小于$this->setTitle等方式设置的规则。
			if ($seo['title']) {
				$title = $seo['title'];
			}
			if ($seo['keywords']) {
				$keywords = $seo['keywords'];
			}
			if ($seo['description']) {
				$description = $seo['description'];
			}
		}
		//生成结果
		$result = array('title' => $title, 'keywords' => $keywords, 'description' => $description);

		//返回结果
		return $result;
    }

	private function getRelatedRules($module, $controller, $action){
		//查询与当前页面相关的SEO规则
		$map = "(app='*' or app='$module') and (controller='*' or controller='$controller') and (action='*' or action='$action') and status=1";

		$rules = $this->where($map)->order('sort asc')->select();

		//返回规则列表
		return $rules;
	}
}