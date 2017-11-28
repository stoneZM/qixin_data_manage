<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\index\controller;

class Base extends \think\Controller {

	protected $url;
	protected $request;
	protected $module;
	protected $controller;
	protected $action;

	public function _initialize() {
		if (!is_file(APP_PATH . 'install.lock')) {
			return $this->redirect('install/index/index');
		}
	}

	protected function setMeta($title = '') {
		$this->assign('meta_title', $title);
	}
	
	protected function setSeo($title = '', $keywords = '', $description = '') {
		$seo = array(
			'title'       => $title,
			'keywords'    => $keywords,
			'description' => $description,
		);
		//获取还没有经过变量替换的META信息
		$meta = model('SeoRule')->getMetaOfCurrentPage($seo);
		foreach ($seo as $key => $item) {
			if (is_array($item)) {
				$item = implode(',', $item);
			}
			$meta[$key] = str_replace("[" . $key . "]", $item . '|', $meta[$key]);
		}

		$data = array(
			'title'       => $meta['title'],
			'keywords'    => $meta['keywords'],
			'description' => $meta['description'],
		);
		$this->assign($data);
	}

	/**
	 * 验证码
	 * @param  integer $id 验证码ID
	 * @author 郭平平 <register@qinfo360.com>
	 */
	public function verify($id = 1) {
		$verify = new \org\Verify(array('length' => 4));
		$verify->entry($id);
	}

	/**
	 * 检测验证码
	 * @param  integer $id 验证码ID
	 * @return boolean     检测结果
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function checkVerify($code, $id = 1) {
		if ($code) {
			$verify = new \org\Verify();
			$result = $verify->check($code, $id);
			if (!$result) {
				return $this->error(lang('Verification_code_error'), "");
			}
		} else {
			return $this->error(lang('Verification_code_is_empty'), "");
		}
	}

	//request信息
	protected function requestInfo() {
		$this->param = $this->request->param();
		defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
		defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
		defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
		defined('IS_POST') or define('IS_POST', $this->request->isPost());
		defined('IS_GET') or define('IS_GET', $this->request->isGet());
		$this->url = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
		$this->assign('request', $this->request);
		$this->assign('param', $this->param);
	}

	/**
	 * 获取单个参数的数组形式
	 */
	protected function getArrayParam($param) {
		if (isset($this->param['id'])) {
			return array_unique((array) $this->param[$param]);
		} else {
			return array();
		}
	}
}
