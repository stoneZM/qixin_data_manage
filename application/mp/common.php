<?php
use think\Session;
use think\Response;
use think\Request;
use think\Url;
use think\DB;
/**
 * 获取当前访问的插件名称

 */
function get_addon() {
    preg_match('/\/mpaddon\/([^\/]+)/', '/'.$_SERVER['PATH_INFO'], $m);
    if (!$m[1]) {
        return false;
    }
    return strtolower($m[1]);
}


function get_nonce($length=32) {
	$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$nonce = '';
	for ($i=0; $i<$length; $i++) {
		$nonce .= $str[mt_rand(0, 61)];
	}
	return $nonce;
}

/**
 * 设置/获取当前公众号标识
 */
function get_mpid($mpid = '') {
    if ($mpid) {
		// 手动设置当前公众号
		Session::set('mpid', intval($mpid));
		Session::set('token',  model('mp')->where(array('id'=>$mpid))->value('token'));
    } elseif (input('mpid')) {   
	    // 如果浏览器中带有公众号标识，则设置为当前公众号	
		Session::set('mpid', intval(input('mpid')));
        Session::set('token', model('mp')->where(array('id'=>input('mpid')))->value('token'));      
    }
    $mpid = Session::get('mpid');                        // 返回当前公众号标识
    if (empty($mpid)) {    
        // 如果公众号标识不存在，则返回0
        return 0;
    }
    return $mpid;
}

/**
 * 设置/获取当前公众号标识
 */
function get_token($token = '') {
    if ($token) {
        Session::set('token', $token);
        Session::set('mpid', model('mp')->where(array('token'=>$token))->value('id'));
    } elseif (input('token')) {
        Session::set('token', input('token'));
        Session::set('mpid', model('mp')->where(array('token'=>input('token')))->value('id'));
    }
    $token = Session::get('token');
    if (empty($token)) {
        return null;
    }
    return $token;
}
/**
 * 获取公众号信息
 */
function get_mp_info($mpid = '') {
    if (empty($mpid)) {
        $mpid = get_mpid();
    }
    $mp_info = model('Mp')->get_mp_info($mpid);
    return $mp_info;
}


/**
 * 设置/获取用户标识
 */
function get_openid($openid = '') {
    $token = get_token();                     
    if (empty($token)) {                         // 如果公众号标识不存在
        return null;
    }
    if ($openid) {                              // 设置当前用户标识
        Session::set('openid_'.$token, $openid);
    } elseif (input('openid')) {                    // 如果浏览器带有openid参数，则缓存用户标识
        Session::set('openid_'.$token, input('openid'));
    }
    $openid = Session::get('openid_'.$token);                 // 获取当前用户标识
    if (empty($openid)) {
        return null;
    }
    return $openid;
}

/**
 * 根据公众号标识获取公众号基本信息
 */
function get_wechat_info($token = '') {
    $token || $token = session('token');                // 获取token
    $wechatInfo = model('mp')->where(array('token'=>$token))->find();
    return $wechatInfo;
}

/**
 * 获取微信api对象
 */
function get_wechat_obj() {
    $wechatInfo = get_mp_info();
    $options = array(
        'token'             =>  $wechatInfo['valid_token'],                 
        'encodingaeskey'    =>  $wechatInfo['encodingaeskey'],      
        'appid'             =>  $wechatInfo['appid'],               
        'appsecret'         =>  $wechatInfo['appsecret']            
    );
    $wechatObj = new \WechatSdk\Wechat($options);
    $wechatObj->getRev();
    return $wechatObj;
}

/**
 * 回复文本消息
 */
function reply_text($text) {
    $wechatObj = get_wechat_obj();
    if (!$text) {
        return;
    }
    return $wechatObj->text($text)->reply();
}

/**
 * 回复图文消息
 */
function reply_news($articles) {
    $wechatObj = get_wechat_obj();
    return $wechatObj->news($articles)->reply();
}

/**
 * 回复音乐消息
 */
function reply_music($arr) {
    if (!isset($arr['title']) || !isset($arr['description']) || !$arr['musicurl']) {
        return false;
    }
    $wechatObj = get_wechat_obj();
    return $wechatObj->music($arr['title'], $arr['description'], $arr['musicurl'], $arr['hgmusicurl'], $arr['thumbmediaid'])->reply();
} 

/**
 * 发送客服消息
 */
function send_custom_message($data) {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->sendCustomMessage($data);
    if (!$result) {
        return $wechatObj->errMsg;
    }
    return $result;
}

function get_menu() {
    $wechatObj = get_wechat_obj();
	
	$result = $wechatObj->getMenu();
    return $result;
}

function create_menu($data) {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->createMenu($data);
    if (!$result) {
        $result['errcode'] = $wechatObj->errCode;
        $result['errmsg'] = $wechatObj->errMsg;
    }
    return $result;
}

function delete_menu() {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->deleteMenu();
    if (!$result) {
        return $wechatObj->errMsg;
    }
    return $result;
}

/**
 * 创建二维码ticket
 * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
 * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
 * @param int $expire 临时二维码有效期，最大为1800秒
 * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800,'url'=>'二维码图片解析后的地址')
 */
function get_qr_code($scene_id,$type=0,$expire=1800){
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->getQRCode($scene_id,$type,$expire);
    if (!$result) {
        $return['errcode'] = 1001;
        $return['errmsg'] = $wechatObj->errMsg;
        return $return;
    }
    return $result;
}

/**
 * 获取二维码图片
 * @param string $ticket 传入由getQRCode方法生成的ticket参数
 * @return string url 返回http地址
 */
function get_qr_url($ticket) {
    $wechatObj = get_wechat_obj();
    return $wechatObj->getQRUrl($ticket);
}

/**
 * 长链接转短链接接口
 * @param string $long_url 传入要转换的长url
 * @return boolean|string url 成功则返回转换后的短url
 */
function get_short_url($long_url){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getShortUrl($long_url);
}

/**
 * 获取接收TICKET
 */
function get_rev_ticket(){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getRevTicket();
}

/**
* 获取二维码的场景值
*/
function get_rev_scene_id(){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getRevSceneId();
}

/**
 * 利用微信接口获取微信粉丝信息
 */
function get_fans_wechat_info($openid = '') {
    $openid || $openid = get_openid();
    $wechatObj = get_wechat_obj();
    return $wechatObj->getUserInfo($openid);
}

/**
 * 获取粉丝基本资料
 */
function get_fans_info($openid = '', $field = '') {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $fans_info = model('MpFans')->get_fans_info($openid, $field);
    if (!$fans_info) {
        return false;
    }
    return $fans_info;
}

/**
 * 获取粉丝头像
 */
function get_fans_headimg($openid = '', $attr = 'width=50 height=50') {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $headimgurl = get_fans_info($openid, 'headimgurl');
    if (empty($headimgurl)) {
        $headimgurl = '__IMG__/noname.jpg';
    }
    return "<img src='".$headimgurl."' ".$attr." />";
}

function get_fans_nickname($openid) {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $nickname = get_fans_info($openid, 'nickname');
    if (empty($nickname)) {
        $nickname = '匿名';
    }
    return $nickname;
}


function get_wechat_img($cover_id, $field = null) {
	
	$SITE_URL = str_replace('/index.php', '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
	if (empty($cover_id)) {
		return $SITE_URL . '/public/images/default.png';
	}
	$picture = db('mp_picture')->where(array('status' => 1, 'id' => $cover_id))->find();
	if ($field == 'path') {
		if (!empty($picture['url'])) {
			$picture['path'] = $picture['url'] ? $SITE_URL . $picture['url'] : $SITE_URL . '/public/images/default.png';
		} else {
			$picture['path'] = $picture['path'] ? $SITE_URL . $picture['path'] : $SITE_URL . '/public/images/default.png';
		}
	}
	return empty($field) ? $picture : $picture[$field];
}

function tomedia($path) {
	
	$SITE_URL = str_replace('/index.php', '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
	
    if (preg_match('/(.*?)\.(jpg|jpeg|png|gif)$/', $path)) {
        return $SITE_URL.$path;
    } else {
        return $SITE_URL . '/public/images/nopic.png';
    }
}

/**
 * 执行sql文件
 */
function execute_sql_file($sql_path) {
    // 读取SQL文件
    $sql = file_get_contents($sql_path);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    
    // 替换表前缀
    $orginal = 'qinfo_';
    $prefix = config('database.prefix');
    $sql = str_replace("{$orginal}", "{$prefix}", $sql);
    // 开始安装
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }
        $res = DB::execute($value);
    }
}
/**
 * 获取微信插件类的类名
 * @param strng $name 插件名
 */
function get_mpaddon_class($name) {
	$class = "\\app\\mp\\addons\\" . strtolower($name) . "\\{$name}";
	return $class;
}


/**
 * 获取微信插件类的配置文件数组
 * @param string $name 插件名
 */
function get_mpaddon_config($name) {
	$class = get_mpaddon_class($name);
	if (class_exists($class)) {
		$addon = new $class();
		return $addon->getConfig();
	} else {
		return array();
	}
}



/**
 * 生成插件访问链接

 */
function create_addon_url($url, $param = array()){
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    $urlArr = explode('/', $url);
	
    switch (count($urlArr)) {
        case 1:
            if (in_array(input('op'), array('mobile', 'admin'))) {
                $act = strtolower(input('op'));
                return url('/mpaddon/'.get_addon().'/'.$act.'/'.$url, $param);
            } else {
                $param['addon'] = get_addon();
                return url('Mp/'.strtolower(input('op')).'/'.$url, $param);
            }
            break;
        case 2:
            if (in_array($urlArr[0], array('mobile', 'admin'))) {
                $act = strtolower($urlArr[0]);
                return url('/mpaddon/'.get_addon().'/'.$act.'/'.$urlArr[1], $param);
            } else {
                $param['addon'] = get_addon();
                return url('Mp/'.$urlArr[0].'/'.$urlArr[1], $param);
            }
            break;
        case 3:
            if (in_array($urlArr[1], array('mobile', 'admin'))) {
                return url('/mpaddon/'.$urlArr[0].'/'.strtolower($urlArr[1]).'/'.$urlArr[2], $param);
            } else {
                $param['addon'] = $urlArr[0];
                return url('Mp/'.$urlArr[1].'/'.$urlArr[2], $param);
            }
            break;
        default:
            return '';
            break;
    }
}

/**
 * 生成移动端访问链接
 */
function create_mobile_url($url, $param = array()) {
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    return url('/mpaddon/'.get_addon().'/mobile/'.$url, $param);
}

/**
 * 生成插件后台访问链接
 */
function create_web_url($url, $param = array()) {
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    return url('/mpaddon/'.get_addon().'/admin/'.$url, $param);
}

/**
 * 获取插件配置信息

 */
function get_addon_settings($addon = '', $mpid = '') {
    if ($addon == '') {
        $addon = get_addon();
    }
    if ($mpid == '') {
        $mpid = get_mpid();
    }
    if (!$addon || !$mpid) {
        return false;
    }
    $addon_settings = model('MpAddonSetting')->get_addon_settings($addon, $mpid);
    if (!$addon_settings) {
        return false;
    }
    return $addon_settings;
}

/**
 * 判断是否处在微信浏览器内
 * @author 艾逗笔<765532665@qq.com>
 */
function is_wechat_browser() {
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (! strpos ( $agent, "icroMessenger" )) {
        return false;
    }
    return true;
}
/**
 * 获取当前访问的完整URL地址
 * @author 艾逗笔<765532665@qq.com>
 */
function get_current_url() {
    $url = 'http://';
    if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
        $url = 'https://';
    }
    if ($_SERVER ['SERVER_PORT'] != '80') {
        $url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
    } else {
        $url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
    }
    // 兼容后面的参数组装
    if (stripos ( $url, '?' ) === false) {
        $url .= '?t=' . time ();
    }
    return $url;
}


/**
 * 初始化粉丝信息
 */
function init_fans() {
    $mp_info = get_mp_info();
    $mpid = get_mpid();
    $openid = get_openid();
    $token = get_token();
    if (empty($openid) && is_wechat_browser() && $mp_info['appid'] && $mp_info['appsecret'] && $mp_info['type'] == 4) {     // 通过网页授权拉取用户标识
        $wechatObj = get_wechat_obj();
        if ($wechatObj->checkAuth($mp_info['appid'], $mp_info['appsecret'])) {              // 公众号有网页授权的权限
            $callback = get_current_url();                  // 当前访问地址
            $redirect_url = $wechatObj->getOauthRedirect($callback);        // 网页授权跳转地址
            if (!input('code')) {                               // 授权跳转第一步
				header("location: ".$redirect_url); 
            } elseif (input('code')) {                          // 授权跳转第二步
                $result = $wechatObj->getOauthAccessToken();
                $user_info = $wechatObj->getOauthUserinfo($result['access_token'], $result['openid']);
                if ($user_info) {
                    $fans_info = model('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$result['openid']))->find();
                    if ($fans_info) {
                        if ($fans_info['is_bind'] !== 1) {
                            $update['nickname'] = $user_info['nickname'];
                            $update['sex'] = $user_info['sex'];
                            $update['country'] = $user_info['country'];
                            $update['province'] = $user_info['province'];
                            $update['city'] = $user_info['city'];
                            $update['headimgurl'] = $user_info['headimgurl'];
                            model('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$result['openid']))->update($update);
                        }
                    } else {
                        $insert['mpid'] = get_mpid();
                        $insert['openid'] = $result['openid'];
                        $insert['is_subscribe'] = 0;
                        $insert['nickname'] = $user_info['nickname'];
                        $insert['sex'] = $user_info['sex'];
                        $insert['country'] = $user_info['country'];
                        $insert['province'] = $user_info['province'];
                        $insert['city'] = $user_info['city'];
                        $insert['headimgurl'] = $user_info['headimgurl'];
                        model('mp_fans')->insert($insert);
                    }
                } 
                Session::set('openid_'.$token, $result['openid']);        // 缓存用户标识
				
				header("location: ".$callback);    // 跳转回原来的地址
            }
        }
    }
}

/**
 * 增加积分
 * @author 艾逗笔<765532665@qq.com>
 */
function add_score($value,$remark='',$type='score',$flag='',$source='addon') {
    return model('MpScoreRecord')->add_score($value,$remark,$type,$flag,$source);
}

/**
 * 获取积分
 */
function get_score($type='', $source='', $flag='', $openid='') {
    return model('MpScoreRecord')->get_score($type, $source, $flag, $openid);
}