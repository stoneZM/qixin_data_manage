<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\mp\controller;
use app\common\controller\Base;

class Mobiles extends Base {

	protected $mpaddons;
	private $signPackage;
	
	public function _initialize() {
		parent::_initialize();
		
		$appid = config('wechat_appid');
		$appsecret = config('wechat_appsecret');
		if($appid  && $appsecret){
			$jssdk = new \WechatSdk\JsSdk($appid,$appsecret);
			$this->signPackage = $jssdk->GetSignPackage();
			$this->signPackage['debug'] = false;
			$this->assign('signPackage',$this->signPackage);	
		}
		if (!is_wechat_browser() && !input('out_trade_no') && $this->wechat_only) {
            $mp_info = get_mp_info();
            if (isset($mp_info['appid'])) {
                $this->redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$mp_info['appid'].'&redirect_uri=&wxref=mp.weixin.qq.com&from=singlemessage&isappinstalled=0&response_type=code&scope=snsapi_base&state=&connect_redirect=1#wechat_redirect');
            } else {
                $this->redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.config('wechat_appid').'&redirect_uri=&wxref=mp.weixin.qq.com&from=singlemessage&isappinstalled=0&response_type=code&scope=snsapi_base&state=&connect_redirect=1#wechat_redirect');
            }
        }
	
		
		if (input('out_trade_no')) {
            $payment = input('post.');
            if (!model('mp_payment')->where(array('orderid'=>$payment['out_trade_no']))->find()) {
                $data['mpid'] = $payment['mpid'];
                $data['openid'] = $payment['openid'];
                $data['orderid'] = $payment['out_trade_no'];
                $data['create_time'] = strtotime($payment['time_end']);
                $data['detail'] = json_encode($payment);
                model('mp_payment')->add($data);
                $return_code = input('return_code');
                $return_msg = input('return_msg');
                return '<xml>
                          <return_code><![CDATA['.$return_code.']]></return_code>
                          <return_msg><![CDATA['.$return_msg.']]></return_msg>
                        </xml>';
            } 
        }
		
		
		if (get_mpid() && !get_openid()) {
            init_fans();
        }
		
		
		$current_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['QUERY_STRING'];
		//处理浏览器openid问题的解决方案
        preg_match('/\/openid[\/|=]([_\-0-9A-Za-z]*+)/', $current_url, $m);		// 带上openid的参数字符串
        if (isset($m[0]) && !empty($m[0])) {
            get_openid($m[1]);                                              // 设置当前用户标识
        	$redirect_url = str_replace($m[0], '', $current_url);			// 去除openid的重定向访问链接
        	$this->redirect($redirect_url);										// 重定向
        }
		
		$this->mpaddons = model('MpAddons');
	}
	
	public function execute($mc = null, $ac = null) {
		
		if (\think\Config::get('url_case_insensitive')) {
			$mc = ucfirst(parse_name($mc, 1));
		}
		if (!empty($mc) && !empty($ac)) {
			$mc = strtolower($mc);
			$class  = "\\app\\mp\\addons\\{$mc}\\controller\\Mobile";
			$addons = new $class;
			$addons->$ac();
		} else {
			$this->error('没有指定插件名称，控制器或操作！');
		}
	}
	
	
	public function template($template='') {
		$mc                         = $this->getAddonsName();
		$ac                         = input('ac', '', 'trim,strtolower');
		$op                         = input('op', 'mobile', 'trim,strtolower');
		$parse_str                  = \think\Config::get('parse_str');
		$parse_str['__ADDONROOT__'] = APP_PATH . "/application/mp/addons/{$mc}";
		\think\Config::set('parse_str', $parse_str);

		if ($template) {
			$template = $template;
		} else {
			$template = $op . "/" . $ac;
		}

		$this->view->engine(
			array('view_path' => "application/mp/addons/" . $mc . "/view/")
		);
		echo $this->fetch($template);
	}
	final public function getAddonsName() {
		$mc = input('mc', '', 'trim,strtolower');
		if ($mc) {
			return $mc;
		} else {
			$class = get_class($this);
			return strtolower(substr($class, strrpos($class, '\\') + 1));
		}
	}
}