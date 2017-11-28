<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

// QinfoCMS常量定义
define('QINFOCORE_VERSION', '3.0.0');
define('QINFOCMS_VERSION', '3.0.0');

define('QINFO_ADDON_PATH', ROOT_PATH . DS . 'addons' . DS);
define('QINFO_MPADDON_PATH', APP_PATH .'mp'. DS . 'addons' . DS);
define('QINFO_MESSAGE_GENER_PATH', EXTEND_PATH  . 'Qmessage' . DS);


//字符串解密加密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4; // 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	// 当此值为 0 时，则不产生随机密钥
	$uc_key = config('data_auth_key') ? config('data_auth_key') : 'qinfocms';
	$key    = md5($key ? $key : $uc_key);
	$keya   = md5(substr($key, 0, 16));
	$keyb   = md5(substr($key, 16, 16));
	$keyc   = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey   = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;

	$string_length = strlen($string);
	$result        = '';
	$box           = range(0, 255);
	$rndkey        = array();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for ($j = $i = 0; $i < 256; $i++) {
		$j       = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp     = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a       = ($a + 1) % 256;
		$j       = ($j + $box[$a]) % 256;
		$tmp     = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}

/**
+----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
+----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
	case 0:
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
		break;
	case 1:
		$chars = str_repeat('0123456789', 3);
		break;
	case 2:
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
		break;
	case 3:
		$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
		break;
	case 4:
		$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
		break;
	default:
		// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
		$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
		break;
	}
	if ($len > 10) {
		//位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
	}
	if ($type != 4) {
		$chars = str_shuffle($chars);
		$str   = substr($chars, 0, $len);
	} else {
		// 中文随机字
		for ($i = 0; $i < $len; $i++) {
			$str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
		}
	}
	return $str;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	if (strlen($slice) == strlen($str)) {
		return $slice;
	} else {
		return $suffix ? $slice . '...' : $slice;
	}
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = array()) {
	\think\Hook::listen($hook, $params);
}

/**
 * 获取广告位广告
 * @param string $name   广告位名称
 * @param mixed $params 传入参数
 * @return html
 */
function ad($name, $param = array()) {
	return widget('common/Ad/run', array('name' => $name));
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name) {
	$class = "\\addons\\" . strtolower($name) . "\\{$name}";
	return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name) {
	$class = get_addon_class($name);
	if (class_exists($class)) {
		$addon = new $class();
		return $addon->getConfig();
	} else {
		return array();
	}
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()) {
	$url        = parse_url($url);
	$case       = config('URL_CASE_INSENSITIVE');
	$addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
	$controller = $case ? parse_name($url['host']) : $url['host'];
	$action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

	/* 解析URL带的参数 */
	if (isset($url['query'])) {
		parse_str($url['query'], $query);
		$param = array_merge($query, $param);
	}

	/* 基础参数 */
	$params = array(
		'mc' => $addons,
		'op' => $controller,
		'ac' => $action,
	);
	$params = array_merge($params, $param); //添加额外参数

	return \think\Url::build('index/addons/execute', $params);
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url) {
	switch ($url) {
	case 'http://' === substr($url, 0, 7):
	case '#' === substr($url, 0, 1):
		break;
	default:
		$url = \think\Url::build($url);
		break;
	}
	return $url;
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null) {
	if (empty($cover_id)) {
		return BASE_PATH . '/public/images/default.png';
	}
	$picture = db('Picture')->where(array('status' => 1, 'id' => $cover_id))->find();
	if ($field == 'path') {
		if (!empty($picture['url'])) {
			$picture['path'] = $picture['url'] ? BASE_PATH . $picture['url'] : BASE_PATH . '/public/images/default.png';
		} else {
			$picture['path'] = $picture['path'] ? BASE_PATH . $picture['path'] : BASE_PATH . '/public/images/default.png';
		}
	}
	return empty($field) ? $picture : $picture[$field];
}

	
/**
 * get_pic_src   渲染图片链接
 * @param $path
 * @return mixed
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function get_pic_src($path)
{
    //不存在http://
    $not_http_remote=(strpos($path, 'http://') === false);
    //不存在https://
    $not_https_remote=(strpos($path, 'https://') === false);
    if ($not_http_remote && $not_https_remote) {
		//本地url
		return '/'.str_replace('//', '/', $path); //防止双斜杠的出现
    } else {
        //远端url
        return $path;
    }
}
	
	
	
function getThumbImage($filename, $width = 100, $height = 100 , $replace = false)
{
    $UPLOAD_URL = '';
    $UPLOAD_PATH = '';
    $filename = str_ireplace($UPLOAD_URL, '', $filename); //将URL转化为本地地址
    $info = pathinfo($filename);
    $oldFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $info['extension'];
    $thumbFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '_' . $width . '_' . $height . '.' . $info['extension'];

    $oldFile = str_replace('\\', '/', $oldFile);
    $thumbFile = str_replace('\\', '/', $thumbFile);

    $filename = ltrim($filename, '/');
   	$oldFile = ltrim($oldFile, '/');
   	$thumbFile = ltrim($thumbFile, '/');

    if (!file_exists($UPLOAD_PATH . $oldFile)) {
        //原图不存在直接返回
        @unlink($UPLOAD_PATH . $thumbFile);
        $info['src'] = $oldFile;
        $info['width'] = intval($width);
        $info['height'] = intval($height);
        return $info;
    } elseif (file_exists($UPLOAD_PATH . $thumbFile) && !$replace) {
        //缩图已存在并且  replace替换为false
        $imageinfo = getimagesize($UPLOAD_PATH . $thumbFile);
        $info['src'] = $thumbFile;
        $info['width'] = intval($imageinfo[0]);
        $info['height'] = intval($imageinfo[1]);
        return $info;
    } else {
        //执行缩图操作
        $oldimageinfo = getimagesize($UPLOAD_PATH . $oldFile);
        $old_image_width = intval($oldimageinfo[0]);
        $old_image_height = intval($oldimageinfo[1]);
        $thumb = \org\Image::init();
        $thumb->open($UPLOAD_PATH . $oldFile);
		$thumb->thumb($width,$height);
		$thumb->save($UPLOAD_PATH . $thumbFile);
        $info['src'] = $UPLOAD_PATH . $thumbFile;
        $info['width'] = $old_image_width;
        $info['height'] = $old_image_height;
        return $info;
    }
}

function get_thumb($cover_id, $width = 100, $height = 100, $replace = false)
{
		$picture = cache('picture_' . $cover_id);
		if (empty($picture)) {
			$picture = db('Picture')->where(array('status' => 1,'id' => $cover_id))->find();
			cache('picture_' . $cover_id, $picture);
		}
		if (empty($picture)) {
			return '/public/images/nopic.png';
		}
		$attach = getThumbImage($picture['path'], $width, $height, $replace);
        return get_pic_src($attach['src']);
}

/**
 * 获取文件
 * @param int $file_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_file($file_id, $field = null) {
	if (empty($file_id)) {
		return '';
	}
	$file = db('File')->where(array('id' => $file_id))->find();
	if ($field == 'path') {
		return $file['savepath'];
	} elseif ($field == 'time') {
		return date('Y-m-d H:i:s', $file['create_time']);
	}
	return empty($field) ? $file : $file[$field];
}

/**
 * 获取多图地址
 * @param array $covers
 * @return 返回图片列表
 * @author molong <register@qinfo360.com>
 */
function get_cover_list($covers) {
	if ($covers == '') {
		return false;
	}
	$cover_list = explode(',', $covers);
	foreach ($cover_list as $item) {
		$list[] = get_cover($item, 'path');
	}
	return $list;
}

/**
 * 获取文件
 * @param int $file_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_goods_file($file_id, $field = null) {
	if (empty($file_id)) {
		return '';
	}
	$file = db('goods_file')->where(array('id' => $file_id))->find();
	if ($field == 'path') {
		return $file['savepath'];
	} elseif ($field == 'time') {
		return date('Y-m-d H:i:s', $file['create_time']);
	}
	return empty($field) ? $file : $file[$field];
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type = 0) {
	if ($type) {
		return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {return strtoupper($match[1]);}, $name));
	} else {
		return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
	}
}

// 不区分大小写的in_array实现
function in_array_case($value, $array) {
	return in_array(strtolower($value), array_map('strtolower', $array));
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
	//数据类型检测
	if (!is_array($data)) {
		$data = (array) $data;
	}
	ksort($data); //排序
	$code = http_build_query($data); //url编码并生成query字符串
	$sign = sha1($code); //生成签名
	return $sign;
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login() {
	$user = session('user_auth');
	if (empty($user)) {
		return 0;
	} else {
		return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
	}
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null) {
	$uid = is_null($uid) ? is_login() : $uid;
	return $uid && (intval($uid) === config('user_administrator'));
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false) {
	$type      = $type ? 1 : 0;
	static $ip = NULL;
	if ($ip !== NULL) {
		return $ip[$type];
	}

	if ($adv) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos = array_search('unknown', $arr);
			if (false !== $pos) {
				unset($arr[$pos]);
			}

			$ip = trim($arr[0]);
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	} elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u", ip2long($ip));
	$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
	$time = $time === NULL ? time() : intval($time);
	return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0) {
	static $list;
	if (!($uid && is_numeric($uid))) {
		//获取当前登录用户名
		return session('user_auth.username');
	}
	$name = db('member')->where(array('uid' => $uid))->value('username');
	return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0) {
	static $list;
	if (!($uid && is_numeric($uid))) {
		//获取当前登录用户名
		return session('user_auth.username');
	}

	/* 获取缓存数据 */
	if (empty($list)) {
		$list = cache('sys_user_nickname_list');
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if (isset($list[$key])) {
		//已缓存，直接使用
		$name = $list[$key];
	} else {
		//调用接口获取用户信息
		$info = db('Member')->field('nickname')->find($uid);
		if ($info !== false && $info['nickname']) {
			$nickname = $info['nickname'];
			$name     = $list[$key]     = $nickname;
			/* 缓存用户 */
			$count = count($list);
			$max   = config('USER_MAX_CACHE');
			while ($count-- > $max) {
				array_shift($list);
			}
			cache('sys_user_nickname_list', $list);
		} else {
			$name = '';
		}
	}
	return $name;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	if (is_array($list)) {
		$refer = $resultSet = array();
		foreach ($list as $i => $data) {
			$refer[$i] = &$data[$field];
		}

		switch ($sortby) {
		case 'asc': // 正向排序
			asort($refer);
			break;
		case 'desc': // 逆向排序
			arsort($refer);
			break;
		case 'nat': // 自然排序
			natcasesort($refer);
			break;
		}
		foreach ($refer as $key => $val) {
			$resultSet[] = &$list[$key];
		}

		return $resultSet;
	}
	return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if (is_array($list) && !is_object($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] = &$list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] = &$list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent             = &$refer[$parentId];
					$parent['childs'][] = $data['id'];
					$parent[$child][]   = &$list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	if (is_array($tree)) {
		foreach ($tree as $key => $value) {
			$reffer = $value;
			if (isset($reffer[$child])) {
				unset($reffer[$child]);
				tree_to_list($value[$child], $child, $order, $list);
			}
			$list[] = $reffer;
		}
		$list = list_sort_by($list, $order, $sortby = 'asc');
	}
	return $list;
}

// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function parse_field_attr($string) {
	if (0 === strpos($string, ':')) {
		// 采用函数定义
		return eval('return ' . substr($string, 1) . ';');
	} elseif (0 === strpos($string, '[')) {
		// 支持读取配置参数（必须是数组类型）
		return \think\Config::get(substr($string, 1, -1));
	}

	$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
	if (strpos($string, ':')) {
		$value = array();
		foreach ($array as $val) {
			list($k, $v) = explode(':', $val);
			$value[$k]   = $v;
		}
	} else {
		$value = $array;
	}
	return $value;
}

function parse_field_bind($table, $selected = '', $model = 0) {
	if ($table) {
		$select = db($table);
		$res    = $select->select();
		foreach ($res as $key => $value) {
			if ($model && $value['model']) {
				$models = explode(',', $value['model']);
				if (in_array($model, $models)) {
					$list[] = $value;
				}
			} else {
				$list[] = $value;
			}
		}
		if (!empty($list)) {
			$tree = new \com\Tree();
			$list = $tree->toFormatTree($list);
		}
	} else {
		$list = array();
	}
	return $list;
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
	$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
	if (strpos($string, ':')) {
		$value = array();
		foreach ($array as $val) {
			list($k, $v) = explode(':', $val);
			$value[$k]   = $v;
		}
	} else {
		$value = $array;
	}
	return $value;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null, $extended_data = null) {

	//参数检查
	if (empty($action) || empty($model) || empty($record_id)) {
		return '参数不能为空';
	}
	if (empty($user_id)) {
		$user_id = is_login();
	}

	//查询行为,判断是否执行
	$action_info = db('Action')->getByName($action);

	if ($action_info['status'] != 1) {
		return '该行为被禁用或删除';
	}

	//插入行为日志
	$data['action_id']   = $action_info['id'];
	$data['action_name']   = $action_info['name'];
	$data['user_id']     = $user_id;
	$data['action_ip']   = ip2long(get_client_ip());
	$data['model']       = $model;
	$data['record_id']   = $record_id;
	$data['create_time'] = time();

	if(!empty($extended_data)){
		$data['extended_data'] = json_encode($extended_data);
	}

	//解析日志规则,生成日志备注
	if (!empty($action_info['log'])) {
		if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
			
			$log['user']   = $user_id;
			$log['record'] = $record_id;
			$log['model']  = $model;
			$log['time']   = time();
			if(!empty($extended_data)){
				$log = array_merge($log,$extended_data);
			}
			foreach ($match[1] as $value) {
				$param = explode('|', $value);
				if (isset($param[1])) {
					$replace[] = call_user_func($param[1], $log[$param[0]]);
				} else {
					$replace[] = $log[$param[0]];
				}
			}
			$data['remark'] = str_replace($match[0], $replace, $action_info['log']);
		} else {
			$data['remark'] = $action_info['log'];
		}
	} else {
		//未定义日志规则，记录操作url
		$data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
	}

	db('ActionLog')->insert($data);

	if (!empty($action_info['rule'])) {
		//解析行为
		$rules = parse_action($action, $user_id);
		//执行行为
		$res = execute_action($rules, $action_info['id'], $user_id);
	}
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self) {
	
	

	if (empty($action)) {
		return false;
	}

	//参数支持id或者name
	if (is_numeric($action)) {
		$map = array('id' => $action);
	} else {
		$map = array('name' => $action);
	}

	//查询行为信息
	$info = db('Action')->where($map)->find();
	if (!$info || $info['status'] != 1) {
		return false;
	}
	
	//解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	$rules  = $info['rule'];
	$rules  = str_replace('{$self}', $self, $rules);
	$rules  = explode(';', $rules);
	
	$return = array();
	foreach ($rules as $key => &$rule) {
		
		if(empty($rule)) continue;
		
		$rule = explode('|', $rule);
		foreach ($rule as $k => $fields) {
			$field = empty($fields) ? array() : explode(':', $fields);
			if (!empty($field)) {
				$return[$key][$field[0]] = $field[1];
			}
		}
		//cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
		if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
			unset($return[$key]['cycle'], $return[$key]['max']);
		}
	}

	return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null) {
	if (!$rules || empty($action_id) || empty($user_id)) {
		return false;
	}

	$return = true;
	foreach ($rules as $rule) {

		//检查执行周期
		$map                = array('action_id' => $action_id, 'user_id' => $user_id);
		$map['create_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
		$exec_count         = db('ActionLog')->where($map)->count();
		if ($exec_count > $rule['max']) {
			continue;
		}
		//执行数据库操作
		$Model = db(ucfirst($rule['table']));
		$field = $rule['field'];
		$res   = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));
		if (!$res) {
			$return = false;
		}
	}
	return $return;
}

function avatar($uid, $size = 'middle') {
	$size = in_array($size, array('big', 'middle', 'small', 'real')) ? $size : 'middle';
	$dir  = setavatardir($uid);
	$file = BASE_PATH . '/uploads/avatar/' . $dir . 'avatar_' . $size . '.png';
	if (!file_exists('.' . $file)) {
		$file = BASE_PATH . '/public/images/default_avatar_' . $size . '.jpg';
	}
	return $file;
}

function setavatardir($uid) {
	$uid  = abs(intval($uid));
	//$uid  = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$dir4 = substr($uid, 7, 2);
	$dir  = $uid.'/';
	if (!is_dir("./uploads/avatar/$dir")) {
		mk_dir("./uploads/avatar/" . $dir);
	}
	return $dir;
}

function mk_dir($dir, $mode = 0755) {
	if (is_dir($dir) || @mkdir($dir, $mode, true)) {
		return true;
	}

	if (!mk_dir(dirname($dir), $mode, true)) {
		return false;
	}

	return @mkdir($dir, $mode, true);
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str = '', $glue = ',') {
	if ($str) {
		return explode($glue, $str);
	} else {
		return array();
	}
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr = array(), $glue = ',') {
	if (empty($arr)) {
		return '';
	} else {
		return implode($glue, $arr);
	}
}
/**
* 将字符串转换为数组
*
* @param    string  $data   字符串
* @return   array   返回数组格式，如果，data为空，则返回空数组
*/
function sqlstr2arr($data) {
    if($data == '') return array();
    @eval("\$array = $data;");
    return $array;
}

/**
* 将数组转换为字符串
*
* @param    array   $data       数组
* @param    bool    $isformdata 如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return   string  返回字符串，如果，data为空，则返回空
*/
function sqlarr2str($data, $isformdata = 1) {
    if($data == '') return '';
    if($isformdata) $data = $data;
    return var_export($data, TRUE);
    //return addslashes(var_export($data, TRUE));
}

/*数据库字符串转为数组*/

function unserialize_config($cfg){
        if (is_string($cfg) ) {
            $arr = sqlstr2arr($cfg);
        $config = array();
        foreach ($arr AS $key => $val) {
            $config[$key] = $val['value'];
        }
        return $config;
    } else {
        return false;
    }
}
/*对象转为数组*/
function objectToArray($e){
	$e=(array)$e;
	foreach($e as $k=>$v){
		if( gettype($v)=='resource' ) return;
		if( gettype($v)=='object' || gettype($v)=='array' )
			$e[$k]=(array)objectToArray($v);
	}
	return $e;
}


/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size,$display_unit=true, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) {
		$size /= 1024;
	}
	if($display_unit == true){
		return  round($size, 2) . $delimiter . $units[$i];
	}else{
		return  round($size, 2);
	}

}
function format_bytes_net($size,$display_unit=true, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1000 && $i < 5; $i++) {
		$size /= 1000;
	}
	if($display_unit == true){
		return  round($size, 2) . $delimiter . $units[$i];
	}else{
		return  round($size, 2);
	}

}
function get_grid_list($list_grids) {
	$grids = preg_split('/[;\r\n]+/s', trim($list_grids));
	foreach ($grids as &$value) {
		// 字段:标题:链接
		$val = explode(':', $value);
		// 支持多个字段显示
		$field = explode(',', $val[0]);
		$value = array('field' => $field, 'title' => $val[1]);
		if (isset($val[2])) {
			// 链接信息
			$value['href'] = $val[2];
			// 搜索链接信息中的字段信息
			preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use (&$fields) {$fields[] = $match[1];}, $value['href']);
		}
		if (strpos($val[1], '|')) {
			// 显示格式定义
			list($value['title'], $value['format']) = explode('|', $val[1]);
		}
		foreach ($field as $val) {
			$array    = explode('|', $val);
			$fields[] = $array[0];
		}
	}
	$data = array('grids' => $grids, 'fields' => $fields);
	return $data;
}

// 获取属性类型信息
function get_attribute_type($type = '') {
	// TODO 可以加入系统配置
	$type_array       = config('config_type_list');
	static $type_list = array();
	foreach ($type_array as $key => $value) {
		
		
		
		$edata = explode(',', $value);
		
		$edata[0] = lang($edata[0]);
		
		$type_list[$key] = $edata;
	}
	return $type ? $type_list[$type][0] : $type_list;
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null) {
	static $list;

	/* 非法分类ID */
	if (!(is_numeric($id) || is_null($id))) {
		return '';
	}

	/* 读取缓存数据 */
	if (empty($list)) {
		$list = cache('document_model_list');
	}

	/* 获取模型名称 */
	if (empty($list)) {
		$map   = array('status' => 1, 'is_bind_category' => 1);
		$model = db('Model')->where($map)->field(true)->select();
		foreach ($model as $value) {
			$list[$value['id']] = $value;
		}
		cache('document_model_list', $list); //更新缓存
	}

	/* 根据条件返回数据 */
	if (is_null($id)) {
		return $list;
	} elseif (is_null($field)) {
		return $list[$id];
	} else {
		return $list[$id][$field];
	}
}

function get_content_status($status) {
	$text = array(
		'-1' => '<span class="label label-danger">删除</span>',
		'0'  => '<span class="label label-default">禁用</span>',
		'1'  => '<span class="label label-primary">正常</span>',
		'2'  => '<span class="label label-info">待审核</span>',
	);
	return $text[$status];
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = null) {
	/* 非法分类ID */
	if (empty($id) || !is_numeric($id)) {
		return '';
	}

	$list = db('Category')->find($id);
	return is_null($field) ? $list : $list[$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id) {
	return get_category($id, 'title');
}

/* 根据ID获取分类名称 */
function get_category_title($id) {
	return get_category($id, 'title');
}

//分类分组
function get_category_list_tree($model) {
	$list = cache('sys_category_list');

	/* 读取缓存数据 */
	if (empty($list)) {
		$list = D('category')->select();
		cache('sys_category_list', $list);
	}
	foreach ($list as $key => $value) {
		if ($model) {
			$models = explode(',', $value['model']);
			if (in_array($model, $models)) {
				$res[] = $value;
			}
		} else {
			$res[] = $value;
		}
	}
	$res  = list_unique($res);
	$tree = list_to_tree($res);
	if ($limit) {
		$tree = array_slice($tree, 0, $limit);
	}
	return $tree;
}

//获取栏目子ID
function get_category_child($id) {
	$list = cache('sys_category_list');

	/* 读取缓存数据 */
	if (empty($list)) {
		$list = db('category')->select();
		cache('sys_category_list', $list);
	}
	$ids[] = $id;
	foreach ($list as $key => $value) {
		if ($value['pid'] == $id) {
			$ids[] = $value['id'];
			$ids   = array_merge($ids, get_category_child($value['id']));
		}
	}
	return array_unique($ids);
}

function send_email($to, $subject, $message) {
	$config = array(
		'protocol'  => 'smtp',
		'smtp_host' => \think\Config::get('mail_host'),
		'smtp_user' => \think\Config::get('mail_username'),
		'smtp_pass' => \think\Config::get('mail_password'),
	);
	$email = new \com\Email($config);
	$email->from(\think\Config::get('mail_fromname'), \think\Config::get('systemconfig.site_title') ? \think\Config::get('systemconfig.site_title') : \think\Config::get('site_title'));
	$email->to($to);

	$email->subject($subject);
	$email->message($message);

	return $email->send();
}

//php获取中文字符拼音首字母
function getFirstCharter($s0) {
	$fchar = ord($s0{0});
	if ($fchar >= ord("A") and $fchar <= ord("z")) {
		return strtoupper($s0{0});
	}

	$s1 = \iconv("UTF-8", "gb2312", $s0);
	$s2 = \iconv("gb2312", "UTF-8", $s1);
	if ($s2 == $s0) {$s = $s1;} else { $s = $s0;}
	$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
	if ($asc >= -20319 and $asc <= -20284) {
		return "A";
	}

	if ($asc >= -20283 and $asc <= -19776) {
		return "B";
	}

	if ($asc >= -19775 and $asc <= -19219) {
		return "C";
	}

	if ($asc >= -19218 and $asc <= -18711) {
		return "D";
	}

	if ($asc >= -18710 and $asc <= -18527) {
		return "E";
	}

	if ($asc >= -18526 and $asc <= -18240) {
		return "F";
	}

	if ($asc >= -18239 and $asc <= -17923) {
		return "G";
	}

	if ($asc >= -17922 and $asc <= -17418) {
		return "H";
	}

	if ($asc >= -17417 and $asc <= -16475) {
		return "J";
	}

	if ($asc >= -16474 and $asc <= -16213) {
		return "K";
	}

	if ($asc >= -16212 and $asc <= -15641) {
		return "L";
	}

	if ($asc >= -15640 and $asc <= -15166) {
		return "M";
	}

	if ($asc >= -15165 and $asc <= -14923) {
		return "N";
	}

	if ($asc >= -14922 and $asc <= -14915) {
		return "O";
	}

	if ($asc >= -14914 and $asc <= -14631) {
		return "P";
	}

	if ($asc >= -14630 and $asc <= -14150) {
		return "Q";
	}

	if ($asc >= -14149 and $asc <= -14091) {
		return "R";
	}

	if ($asc >= -14090 and $asc <= -13319) {
		return "S";
	}

	if ($asc >= -13318 and $asc <= -12839) {
		return "T";
	}

	if ($asc >= -12838 and $asc <= -12557) {
		return "W";
	}

	if ($asc >= -12556 and $asc <= -11848) {
		return "X";
	}

	if ($asc >= -11847 and $asc <= -11056) {
		return "Y";
	}

	if ($asc >= -11055 and $asc <= -10247) {
		return "Z";
	}

	return null;
}

function PyFirst($zh) {
	$ret = "";
	$s1  = \iconv("UTF-8", "gb2312", $zh);
	$s2  = \iconv("gb2312", "UTF-8", $s1);
	if ($s2 == $zh) {$zh = $s1;}
	for ($i = 0; $i < strlen($zh); $i++) {
		$s1 = substr($zh, $i, 1);
		$p  = ord($s1);
		if ($p > 160) {
			$s2 = substr($zh, $i++, 2);
			$ret .= getFirstCharter($s2);
		} else {
			$ret .= $s1;
		}
	}
	return $ret;
}

/*
function:二维数组按指定的键值排序
author:www.111cn.net
*/
function array_sort($array,$keys,$type='asc'){
	if(!isset($array) || !is_array($array) || empty($array)){
		return '';
	}
	if(!isset($keys) || trim($keys)==''){
		return '';
	}
	if(!isset($type) || $type=='' || !in_array(strtolower($type),array('asc','desc'))){
		return '';
	}
	$keysvalue=array();
	foreach($array as $key=>$val){
		$val[$keys] = str_replace('-','',$val[$keys]);
		$val[$keys] = str_replace(' ','',$val[$keys]);
		$val[$keys] = str_replace(':','',$val[$keys]);
		$keysvalue[] =$val[$keys];
	}
	asort($keysvalue); //key值排序
	reset($keysvalue); //指针重新指向数组第一个
	foreach($keysvalue as $key=>$vals) {
		$keysort[] = $key;
	}
	$keysvalue = array();
	$count=count($keysort);
	if(strtolower($type) != 'asc'){
		for($i=$count-1; $i>=0; $i--) {
			$keysvalue[] = $array[$keysort[$i]];
		}
	}else{
		for($i=0; $i<$count; $i++){
			$keysvalue[] = $array[$keysort[$i]];
		}
	}
	return $keysvalue;
}

function tab($step = 1, $string = ' ', $size = 4)
{
    return str_repeat($string, $size * $step);
}

function int_to_string(&$data, $map = array('status' => array(1 => '正常', -1 => '删除', 0 => '禁用', 2 => '未审核', 3 => '草稿'))) {
	if ($data === false || $data === null) {
		return $data;
	}
	$data = (array) $data;
	foreach ($data as $key => $row) {
		foreach ($map as $col => $pair) {
			if (isset($row[$col]) && isset($pair[$row[$col]])) {
				$data[$key][$col . '_text'] = $pair[$row[$col]];
			}
		}
	}
	return $data;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_status_title($status = null) {
	if (!isset($status)) {
		return false;
	}
	switch ($status) {
	case -1:return '已删除';
		break;
	case 0:return '禁用';
		break;
	case 1:return '正常';
		break;
	case 2:return '待审核';
		break;
	default:return false;
		break;
	}
}

// 获取数据的状态操作
function show_status_op($status) {
	switch ($status) {
	case 0:return lang('enable');
		break;
	case 1:return lang('disable');
		break;
	case 2:return lang('review');
		break;
	default:return false;
		break;
	}
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author huajie <banhuajie@163.com>
 */
function get_action_type($type, $all = false) {
	$list = array(
		1 => lang('system'),
		2 => lang('user'),
	);
	if ($all) {
		return $list;
	}
	return $list[$type];
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 * @author huajie <banhuajie@163.com>
 */
function get_action($id = null, $field = null) {
	if (empty($id) && !is_numeric($id)) {
		return false;
	}
	$list = cache('action_list');
	if (empty($list[$id])) {
		$map       = array('status' => array('gt', -1), 'id' => $id);
		$list[$id] = db('Action')->where($map)->field(true)->find();
	}
	return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @author huajie <banhuajie@163.com>
 */
function get_document_field($value = null, $condition = 'id', $field = null) {
	if (empty($value)) {
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info            = db('Model')->where($map);
	if (empty($field)) {
		$info = $info->field(true)->find();
	} else {
		$info = $info->value($field);
	}
	return $info;
}

/**
 * CURLFILE 兼容性处理 php < 5.5
 * 一定不要修改、删除，否则 curl 可能无法上传文件
 */
if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '')
    {
        return "@$filename;filename="
        . ($postname ?: basename($filename))
        . ($mimetype ? ";type=$mimetype" : '');
    }
}

/**
 * flash message
 *
 * flash("?KEY") 判断是否存在flash message KEY 返回bool值
 * flash("KEY") 获取flash message，存在返回具体值，不存在返回null
 * flash("KEY","VALUE") 设置flash message
 * @param string $key
 * @param bool|string $value
 * @return bool|mixed|null
 */
function flash($key, $value = false)
{
    $prefix = 'flash_';
    // 判断是否存在flash message
    if ('?' == substr($key, 0, 1)) {
        return Session::has($prefix . substr($key, 1));
    } else {
        $flash_key = $prefix . $key;
        if (false === $value) {
            // 获取flash
            $ret = Session::pull($flash_key);

            return null === $ret ? null : unserialize($ret);
        } else {
            // 设置flash
            return Session::set($flash_key, serialize($value));
        }
    }
}

/**
 * 表格排序筛选
 * @param string $name  单元格名称
 * @param string $field 排序字段
 * @return string
 */
function sort_by($name, $field = '')
{
    $sort = Request::instance()->param('_sort');
    $param = Request::instance()->get();
    $param['_sort'] = ($sort == 'asc' ? 'desc' : 'asc');
    $param['_order'] = $field;
    $url = Url::build(Request::instance()->action(), $param);

    return Request::instance()->param('_order') == $field ?
        "<a href='{$url}' title='点击排序' class='sorting-box sorting-{$sort}'>{$name}</a>" :
        "<a href='{$url}' title='点击排序' class='sorting-box sorting'>{$name}</a>";
}

/**
 * 用于高亮搜索关键词
 * @param string $string 原文本
 * @param string $needle 关键词
 * @param string $class  span标签class名
 * @return mixed
 */
function high_light($string, $needle = '', $class = 'c-red')
{
    return $needle !== '' ? str_replace($needle, "<span class='{$class}'>" . $needle . "</span>", $string) : $string;
}

/**
 * 用于显示状态操作按钮
 * @param int $status        0|1|-1状态
 * @param int $id            对象id
 * @param string $field      字段，默认id
 * @param string $controller 默认当前控制器
 * @return string
 */
function show_status($status, $id, $field = 'id', $controller = '')
{
    $controller === '' && $controller = Request::instance()->controller();
    switch ($status) {
        // 恢复
        case 0 :
            $ret = '<a href="javascript:;" onclick="ajax_req(\'' . Url::build($controller . '/resume', [$field => $id]) . '\',{},change_status,[this,\'resume\'])" class="label label-success radius" title="点击恢复">恢复</a>';
            break;
        // 禁用
        case 1 :
            $ret = '<a href="javascript:;" onclick="ajax_req(\'' . Url::build($controller . '/forbid', [$field => $id]) . '\',{},change_status,[this,\'forbid\'])" class="label label-warning radius" title="点击禁用">禁用</a>';
            break;
        // 还原
        case -1 :
            $ret = '<a href="javascript:;" onclick="ajax_req(\'' . Url::build($controller . '/recycle', [$field => $id]) . '\')" class="label label-secondary radius" title="点击还原">还原</a>';
            break;
    }

    return $ret;
}

/**
 * 显示状态
 * @param int $status     0|1|-1
 * @param bool $imageShow true只显示图标|false只显示文字
 * @return string
 */
function get_status($status, $imageShow = true)
{
    switch ($status) {
        case 0 :
            $showText = '禁用';
            $showImg = '<i class="Hui-iconfont c-warning status" title="禁用">&#xe631;</i>';
            break;
        case -1 :
            $showText = '删除';
            $showImg = '<i class="Hui-iconfont c-danger status" title="删除">&#xe6e2;</i>';
            break;
        case 1 :
        default :
            $showText = '正常';
            $showImg = '<i class="Hui-iconfont c-success status" title="正常">&#xe615;</i>';

    }

    return ($imageShow === true) ? $showImg : $showText;
}

/**
 * 框架内部默认ajax返回
 * @param string $msg      提示信息
 * @param string $redirect 重定向类型 current|parent|''
 * @param string $alert    父层弹框信息
 * @param bool $close      是否关闭当前层
 * @param string $url      重定向地址
 * @param string $data     附加数据
 * @param int $code        错误码
 * @param array $extend    扩展数据
 */
function ajax_return_adv($msg = '操作成功', $redirect = 'parent', $alert = '', $close = false, $url = '', $data = '', $code = 0, $extend = [])
{
    $extend['opt'] = [
        'alert'    => $alert,
        'close'    => $close,
        'redirect' => $redirect,
        'url'      => $url,
    ];

    return ajax_return($data, $msg, $code, $extend);
}

/**
 * 返回错误json信息
 */
function ajax_return_adv_error($msg = '', $code = 1, $redirect = '', $alert = '', $close = false, $url = '', $data = '', $extend = [])
{
    return ajax_return_adv($msg, $alert, $close, $redirect, $url, $data, $code, $extend);
}

/**
 * ajax数据返回，规范格式
 * @param array $data   返回的数据，默认空数组
 * @param string $msg   信息
 * @param int $code     错误码，0-未出现错误|其他出现错误
 * @param array $extend 扩展数据
 */
function ajax_return($data = [], $msg = "", $code = 0, $extend = [])
{
    $ret = ["code" => $code, "msg" => $msg, "data" => $data];
    $ret = array_merge($ret, $extend);

    return json($ret);
}

/**
 * 返回标准错误json信息
 */
function ajax_return_error($msg = "出现错误", $code = 1, $data = [], $extend = [])
{
    return ajax_return($data, $msg, $code, $extend);
}

/**
 * 从二维数组中取出自己要的KEY值
 * @param  array $arrData
 * @param string $key
 * @param $im true 返回逗号分隔
 * @return array
 */
function filter_value($arrData, $key, $im = false)
{
    $re = [];
    foreach ($arrData as $k => $v) {
        if (isset($v[$key])) $re[] = $v[$key];
    }
    if (!empty($re)) {
        $re = array_flip(array_flip($re));
        sort($re);
    }

    return $im ? implode(',', $re) : $re;
}

/**
 * 重设键，转为array(key=>array())
 * @param array $arr
 * @param string $key
 * @return array
 */
function reset_by_key($arr, $key)
{
    $re = [];
    foreach ($arr as $v) {
        $re[$v[$key]] = $v;
    }

    return $re;
}
/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey = "", $pCondition = "")
{
    $result = array();
    if (is_array($pArray)) {
        foreach ($pArray as $temp_array) {
            if (is_object($temp_array)) {
                $temp_array = (array)$temp_array;
            }
            if (("" != $pCondition && $temp_array[$pCondition[0]] == $pCondition[1]) || "" == $pCondition) {
                $result[] = ("" == $pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
            }
        }
        return $result;
    } else {
        return false;
    }
}

/**
 * 统一密码加密方式，如需变动直接修改此处
 * @param $password
 * @return string
 */
function password_hash_tp($password)
{
    return hash("md5", trim($password));
}

/**
 * 生成随机字符串
 * @param string $prefix
 * @return string
 */
function get_random($prefix = '')
{
    return $prefix . base_convert(time() * 1000, 10, 36) . "_" . base_convert(microtime(), 10, 36) . uniqid();
}

/**
 * 获取自定义配置
 * @param string|int $name 配置项的key或者value，传key返回value，传value返回key
 * @param string $conf
 * @param bool $key        传递的是否是配置键名，默认是，则返回配置信息
 * @return int|string
 */
function get_conf($name, $conf, $key = true)
{
    $arr = config("conf." . $conf);
    if ($key) return $arr[$name];
    foreach ($arr as $k => $v) {
        if ($v == $name) {
            return $k;
        }
    }
}


/**
 * 多维数组合并（支持多数组）
 * @return array
 */
function array_merge_multi()
{
    $args = func_get_args();
    $array = [];
    foreach ($args as $arg) {
        if (is_array($arg)) {
            foreach ($arg as $k => $v) {
                if (is_array($v)) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : [];
                    $array[$k] = array_merge_multi($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }

    return $array;
}

/**
 * 读取错误信息
 * @return mixed
 */
function check_message_data($data)//nState nytpe nResult
{
	$ret_msg = '';
	switch($data)
	{
		case 1: 
			$ret_msg = '1';
			break;
		case 2: 
			$ret_msg = "存储不在线";
			break;
		case 3: 
			$ret_msg = "客户端不在线";
			break;
		case 4: 
			$ret_msg = "磁盘复制引擎关闭";
			break;
		case 5: 
			$ret_msg = "存储路径是空的，可以不为空";
			break;
	}
	
	return $ret_msg;	
}

/**
 * 友好的时间显示
 *
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 */
function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if($type=='normal'){
        if( $dTime < 60 ){
            if($dTime < 10){
                return '刚刚';    //by yangjs
            }else{
                return intval(floor($dTime / 10) * 10).'秒前';
            }
        }elseif( $dTime < 3600 ){
            return intval($dTime/60).'分钟前';
            //今天的数据.年份相同.日期相同.
        }elseif( $dYear==0 && $dDay == 0  ){
            //return intval($dTime/3600).'小时前';
            return '今天'.date('H:i',$sTime);
        }elseif($dYear==0){
            return date("m月d日 H:i",$sTime);
        }else{
            return date("Y-m-d H:i",$sTime);
        }
    }elseif($type=='mohu'){
        if( $dTime < 60 ){
            return $dTime.'秒前';
        }elseif( $dTime < 3600 ){
            return intval($dTime/60).'分钟前';
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600).'小时前';
        }elseif( $dDay > 0 && $dDay<=7 ){
            return intval($dDay).'天前';
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '周前';
        }elseif( $dDay > 30 ){
            return intval($dDay/30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    }elseif($type=='full'){
        return date("Y-m-d , H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y-m-d",$sTime);
    }else{
        if( $dTime < 60 ){
            return $dTime.'秒前';
        }elseif( $dTime < 3600 ){
            return intval($dTime/60).'分钟前';
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600).'小时前';
        }elseif($dYear==0){
            return date("Y-m-d H:i:s",$sTime);
        }else{
            return date("Y-m-d H:i:s",$sTime);
        }
    }
}

function formatLog($log)
{
    $log = explode("\r\n", $log);
    $log = '<li>' . implode('</li><li>', $log) . '</li>';
    return $log;
}

/**
 * t函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
function op_t($text, $addslanshes = false)
{
    $text = nl2br($text);
    $text = real_strip_tags($text);
    if ($addslanshes)
        $text = addslashes($text);
    $text = trim($text);
    return $text;
}

/**过滤函数，别名函数，op_t的别名
 * @param $text
 * @auth 陈一枭
 */
function text($text, $addslanshes = false)
{
    return op_t($text, $addslanshes);
}

/**过滤函数，别名函数，op_h的别名
 * @param $text
 * @auth 陈一枭
 */
function html($text)
{
    return op_h($text);
}

/**
 * h函数用于过滤不安全的html标签，输出安全的html
 * @param string $text 待过滤的字符串
 * @param string $type 保留的标签格式
 * @return string 处理后内容
 */
function op_h($text, $type = 'html')
{
    // 无标签格式
    $text_tags = '';
    //只保留链接
    $link_tags = '<a>';
    //只保留图片
    $image_tags = '<img>';
    //只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    //标题摘要基本格式
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    //兼容Form格式
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    //内容等允许HTML的格式
    $html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    //专题等全HTML格式
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><meta><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    //过滤标签
    $text = real_strip_tags($text, ${$type . '_tags'});
    // 过滤攻击代码
    if ($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background[^-]|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }
    return $text;
}

function real_strip_tags($str, $allowable_tags = "")
{
    // $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    return strip_tags($str, $allowable_tags);
}



//字符串解密加密
function licensecrypto($string, $operation = 'DECODE', $key = '') {
	$ckey_length = 4; // 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	// 当此值为 0 时，则不产生随机密钥
	$key    = md5($key ? $key : 'qinfocms');

	$keya   = md5(substr($key, 0, 16));
	$keyb   = md5(substr($key, 16, 16));
	$keyc   = ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(time()), -$ckey_length));

	$cryptkey   = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', 0) . substr(md5($string . $keyb), 0, 16) . $string;

	$string_length = strlen($string);
	$result        = '';
	$box           = range(0, 255);
	$rndkey        = array();

	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for ($j = $i = 0; $i < 256; $i++) {
		$j       = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp     = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a       = ($a + 1) % 256;
		$j       = ($j + $box[$a]) % 256;
		$tmp     = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}




function get_license()
{
    $license_path = ROOT_PATH . 'data/license/license';
	$license_conf = @file_get_contents($license_path);
	if($license_conf){
		$license_data = json_decode(licensecrypto($license_conf),true);
		return $license_data;
	}else{
		return false;
	}
}
function get_storage_net_size(){

	$license = get_license();
	$storage = $license['config_info']['storage'];
	if($storage['storage_net_size']){
		return $storage['storage_net_size'];
	}else{
		return 0;
	}

}

function set_license($license_code)
{
	
	if(!$license_code){
		return false;
	}
    $license_path = ROOT_PATH . 'data/license/license';
	$license_data = @file_put_contents($license_path, $license_code);
	
	if($license_data){
		return true;
	}else{
		return false;
	}
}


function get_hardware()
{
    $license_path = ROOT_PATH . 'data/license/hardware';
	$license_conf = @file_get_contents($license_path);
	if($license_conf){
		$license_data = json_decode(licensecrypto($license_conf),true);
		return $license_data;
	}else{
		return false;
	}
    
}


function set_license_png($license_sn)
{
	
	if(!$license_sn){
		return false;
	}
	$img_path = ROOT_PATH . 'data/license/license.png';
	if(file_exists($img_path)== true){
		@unlink ($img_path);
	}
	
	$qrCode = new \Endroid\QrCode\QrCode();
	//想显示在二维码中的文字内容，这里设置了一个查看文章的地址
	$url = url('/license',array('sn'=>$license_sn),true,config('mobile_url'));
	$qrCode->setText($url)
		->setSize(300)
		->setPadding(10)
		->setErrorCorrection('high')
		->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
		->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
		->setLabelFontPath(ROOT_PATH . 'public/fonts/wqy-microhei.ttc')
		->setLabel('微信扫一扫，售后找客服')
		->setLabelFontSize(18)
		->setImageType(\Endroid\QrCode\QrCode::IMAGE_TYPE_PNG);
	//$qrCode->render();
	$qrCode->save($img_path);
	if(file_exists($img_path)== true){
		return true;
	}else{
		return false;
	}
}

function remove_license_png()
{
	$img_path = ROOT_PATH . 'data/license/license.png';
	if(file_exists($img_path)== true){
		@unlink ($img_path);
	}
}

function get_device_macdesc($val){
	$str_tmp =  $val;//$val['device_mac'];
	$sCardMac = substr($str_tmp,0,2) . ':' . substr($str_tmp,2,2). ':' . substr($str_tmp,4,2) . ':' . substr($str_tmp,6,2). ':' . substr($str_tmp,8,2). ':' . substr($str_tmp,10,2);
	return $sCardMac;
}

function get_libvirt_url($ip){

	$url = 'qemu+tcp://'.$ip.'/system';
	return $url;
}
function updata_system_menu($db, $prefix){
	
	
	$license_data = get_license();
	
	$version_type = $license_data['version_type'];
	
	if($version_type == 'liter'){
		$sql = array(
			0 => "UPDATE `[PREFIX]menu` SET hide= 1  where title = 'device_group'",
			1 => "UPDATE `[PREFIX]menu` SET hide= 1  where title = 'storage_pool'",
			2 => "UPDATE `[PREFIX]menu` SET hide= 1  where title = 'compute_pool'",
			3 => "UPDATE `[PREFIX]menu` SET hide= 1  where title = 'cloud_market'",
			4 => "UPDATE `[PREFIX]menu` SET hide= 1  where title = 'auto_update'",
		);
	}else if($version_type == 'base'){
		$sql = array(
			0 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'device_group'",
			1 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'storage_pool'",
			2 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'compute_pool'",
			3 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'cloud_market'",
			4 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'auto_update'",
			
		);
	}else if($version_type == 'high'){
		$sql = array(
			0 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'device_group'",
			1 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'storage_pool'",
			2 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'compute_pool'",
			3 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'cloud_market'",
			4 => "UPDATE `[PREFIX]menu` SET hide= 0  where title = 'auto_update'",
		);
	}

	if($sql){
		foreach ($sql as $value) {
			$value = trim($value);
			$value = str_replace(array('[PREFIX]'),array($prefix),$value);
			$db->execute($value);	
		}
	}
}

function is_liter_system(){
	$system_type = get_license()['version_type'];
	return ($system_type == "liter");
}
function is_base_system(){
	$system_type = get_license()['version_type'];
	return ($system_type == "base");
}
function is_high_system(){
	$system_type = get_license()['version_type'];
	return ($system_type == "high");
}

function get_soft_type(){

	$system_type = get_license()['version_type'];
	if($system_type == "liter")
		return 0;
	if($system_type == "base")
		return 1;
	if ($system_type == "high")
		return 2;

}


function get_log_list($map=null){

	$log_data = db('action_log')->where($map)->order('id desc')->limit(0,30)->select();
	if($log_data){
		$action_data = db('action')->where(array('status'=>1))->order('id desc')->column('name,title','id');
		foreach ($log_data as $key => &$log) {
			$log['action_title'] = $action_data[$log['action_id']]['title'];
			$r_log[date('Y-m-d',$log['create_time'])][]=$log;//分组
		}
		return $r_log;
	}
	return false;
}

function checkrule($rule, $type = 2, $mode = 'url') {

	
	if(is_administrator()){
		return true;
	}
	$Auth = new \com\Auth();
	if (!$Auth->check($rule, session('user_auth.uid'), $type, $mode)) {
		return false;
	}
	return true;
}

//	更新 hardware info
function update_hardware_info(){

	$message_str = "w2p_update_local_machine_hardware";
	\Netmessage::send_message($message_str,array());

}