-- -----------------------------
-- 表结构 `qinfo_mp`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `group_id` varchar(50) DEFAULT NULL COMMENT '可用套餐ID',
  `name` varchar(50) NOT NULL COMMENT '公众号名称',
  `origin_id` varchar(50) NOT NULL COMMENT '公众号原始ID',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '公众号类型（1：普通订阅号；2：认证订阅号；3：普通服务号；4：认证服务号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常，2：审核中）',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `valid_token` varchar(40) DEFAULT NULL COMMENT '接口验证Token',
  `token` varchar(50) DEFAULT NULL COMMENT '公众号标识',
  `encodingaeskey` varchar(50) DEFAULT NULL COMMENT '消息加解密秘钥',
  `appid` varchar(50) DEFAULT NULL COMMENT 'AppId',
  `appsecret` varchar(50) DEFAULT NULL COMMENT 'AppSecret',
  `mp_number` varchar(50) DEFAULT NULL COMMENT '微信号',
  `desc` text COMMENT '描述',
  `headimg` varchar(255) DEFAULT NULL COMMENT '头像',
  `qrcode` varchar(255) DEFAULT NULL COMMENT '二维码',
  `login_name` varchar(50) DEFAULT NULL COMMENT '公众号登录名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='公众号表';


-- -----------------------------
-- 表结构 `qinfo_mp_addon_entry`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_addon_entry` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `addon` varchar(50) NOT NULL COMMENT '插件名称',
  `name` varchar(255) DEFAULT NULL COMMENT '入口名称',
  `act` varchar(50) NOT NULL COMMENT '操作',
  `title` varchar(255) NOT NULL COMMENT '封面标题',
  `desc` text COMMENT '封面描述',
  `cover` varchar(255) NOT NULL DEFAULT '0' COMMENT '封面图片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件功能入口表';


-- -----------------------------
-- 表结构 `qinfo_mp_addon_setting`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_addon_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `addon` varchar(50) NOT NULL COMMENT '插件标识',
  `name` varchar(50) NOT NULL COMMENT '配置项',
  `value` text NOT NULL COMMENT '配置值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件配置参数表';


-- -----------------------------
-- 表结构 `qinfo_mp_addons`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_addons` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(255) NOT NULL COMMENT '插件名称',
  `bzname` varchar(50) NOT NULL COMMENT '标识名',
  `desc` text COMMENT '描述',
  `version` varchar(10) NOT NULL COMMENT '版本号',
  `author` varchar(50) NOT NULL COMMENT '作者姓名',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `config` text COMMENT '插件配置',
  `type` varchar(50) DEFAULT NULL COMMENT '插件分类',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件表';


-- -----------------------------
-- 表结构 `qinfo_mp_addons_access`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_addons_access` (
  `user_id` int(10) NOT NULL,
  `addon` varchar(50) NOT NULL,
  `mpid` int(10) NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_mp_auto_reply`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_auto_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `type` varchar(50) DEFAULT '' COMMENT '回复场景',
  `reply_type` varchar(50) DEFAULT '' COMMENT '回复类型',
  `material_id` int(10) DEFAULT NULL COMMENT '回复素材ID',
  `keyword` varchar(50) DEFAULT '' COMMENT '绑定的关键词',
  `addon` varchar(50) DEFAULT '' COMMENT '处理消息的插件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号自动回复表';


-- -----------------------------
-- 表结构 `qinfo_mp_donate_list`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_donate_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户标识',
  `money` float(10,2) DEFAULT NULL COMMENT '捐赠额',
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否匿名',
  `pay_status` int(1) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `create_time` int(10) DEFAULT NULL COMMENT '捐赠时间',
  `content` text COMMENT '留言内容',
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示',
  `orderid` varchar(50) DEFAULT NULL COMMENT '订单号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微捐赠插件捐赠列表';


-- -----------------------------
-- 表结构 `qinfo_mp_fans`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_fans` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(255) NOT NULL COMMENT '粉丝标识',
  `is_subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关注',
  `subscribe_time` int(10) DEFAULT NULL COMMENT '关注时间',
  `unsubscribe_time` int(10) DEFAULT NULL COMMENT '取消关注时间',
  `nickname` varchar(50) DEFAULT NULL COMMENT '粉丝昵称',
  `sex` tinyint(1) DEFAULT NULL COMMENT '粉丝性别',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '粉丝头像',
  `relname` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `signature` text COMMENT '个性签名',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `is_bind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否绑定',
  `language` varchar(50) DEFAULT NULL COMMENT '使用语言',
  `country` varchar(50) DEFAULT NULL COMMENT '国家',
  `province` varchar(50) DEFAULT NULL COMMENT '身份',
  `city` varchar(50) DEFAULT NULL COMMENT '城市',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `groupid` int(10) DEFAULT NULL COMMENT '分组ID',
  `tagid_list` varchar(255) DEFAULT NULL COMMENT '标签',
  `score` int(10) DEFAULT '0' COMMENT '积分',
  `money` int(10) DEFAULT '0' COMMENT '金钱',
  `latitude` varchar(50) DEFAULT NULL COMMENT '纬度',
  `longitude` varchar(50) DEFAULT NULL COMMENT '经度',
  `location_precision` varchar(50) DEFAULT NULL COMMENT '精度',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号粉丝表';


-- -----------------------------
-- 表结构 `qinfo_mp_feedback`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_feedback` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户标识',
  `name` varchar(255) DEFAULT NULL COMMENT '反馈者姓名',
  `contact` varchar(255) NOT NULL COMMENT '联系方式内容',
  `contact_type` tinyint(1) DEFAULT NULL COMMENT '联系方式类型',
  `content` text COMMENT '反馈内容',
  `create_time` int(10) NOT NULL COMMENT '反馈时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈表';


-- -----------------------------
-- 表结构 `qinfo_mp_group`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(255) NOT NULL COMMENT '套餐名称',
  `addons` text COMMENT '可管理的插件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号套餐表';


-- -----------------------------
-- 表结构 `qinfo_mp_material`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_material` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `type` varchar(50) DEFAULT NULL COMMENT '素材类型',
  `content` text COMMENT '文本素材内容',
  `image` varchar(255) DEFAULT NULL COMMENT '图片素材路径',
  `title` varchar(255) DEFAULT NULL COMMENT '图文素材标题',
  `picurl` varchar(255) DEFAULT NULL COMMENT '图文素材封面',
  `url` varchar(255) DEFAULT NULL COMMENT '图文链接',
  `description` text COMMENT '图文素材描述',
  `detail` text COMMENT '图文素材详情',
  `create_time` int(10) DEFAULT NULL COMMENT '素材创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号素材表';


-- -----------------------------
-- 表结构 `qinfo_mp_message`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(50) NOT NULL COMMENT '用户标识',
  `msgid` varchar(50) DEFAULT NULL COMMENT '消息ID',
  `msgtype` varchar(10) NOT NULL COMMENT '消息类型',
  `content` text COMMENT '消息内容',
  `create_time` int(10) NOT NULL COMMENT '消息发送时间',
  `picurl` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `mediaid` varchar(255) DEFAULT NULL COMMENT '媒体ID',
  `format` varchar(50) DEFAULT NULL COMMENT '语音格式',
  `recognition` text COMMENT '语音识别结果',
  `thumb_mediaid` varchar(255) DEFAULT NULL COMMENT '视频消息缩略图ID',
  `location_x` float DEFAULT NULL COMMENT '地理位置纬度',
  `location_y` float DEFAULT NULL COMMENT '地理位置精度',
  `scale` int(5) DEFAULT NULL COMMENT '地图缩放大小',
  `label` varchar(50) DEFAULT NULL COMMENT '地理位置信息',
  `title` varchar(255) DEFAULT NULL COMMENT '链接消息标题',
  `description` varchar(255) DEFAULT NULL COMMENT '链接消息描述',
  `url` varchar(255) DEFAULT NULL COMMENT '链接消息地址',
  `reply_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '回复状态',
  `save_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '保存为素材状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息表';


-- -----------------------------
-- 表结构 `qinfo_mp_payment`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_payment` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户标识',
  `orderid` varchar(255) DEFAULT NULL COMMENT '订单号',
  `create_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `detail` text COMMENT '支付详情',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号支付配置';


-- -----------------------------
-- 表结构 `qinfo_mp_picture`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_mp_rule`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_rule` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号ID',
  `addon` varchar(50) DEFAULT NULL COMMENT '插件标识',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词内容',
  `type` varchar(50) DEFAULT NULL COMMENT '触发类型',
  `entry_id` int(10) DEFAULT NULL COMMENT '功能入口ID',
  `reply_id` int(10) DEFAULT NULL COMMENT '自动回复ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号响应规则';


-- -----------------------------
-- 表结构 `qinfo_mp_scene_qrcode`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_scene_qrcode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `scene_name` varchar(255) DEFAULT NULL COMMENT '场景名称',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关联关键词',
  `scene_type` char(10) DEFAULT '0' COMMENT '二维码类型',
  `scene_id` int(32) DEFAULT NULL COMMENT '场景值ID',
  `scene_str` varchar(255) DEFAULT NULL COMMENT '场景值字符串',
  `expire` int(10) DEFAULT NULL COMMENT '过期时间',
  `ticket` varchar(255) DEFAULT NULL COMMENT '二维码Ticket',
  `url` varchar(255) DEFAULT NULL COMMENT '二维码图片解析后的地址',
  `ctime` int(10) DEFAULT NULL COMMENT '二维码创建时间',
  `short_url` varchar(255) DEFAULT NULL COMMENT '二维码短地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_mp_scene_qrcode_statistics`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_scene_qrcode_statistics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '扫码者openid',
  `scene_name` varchar(255) DEFAULT NULL COMMENT '场景名称',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关联关键词',
  `scene_id` varchar(255) DEFAULT NULL COMMENT '场景ID/场景字符串',
  `scan_type` varchar(255) DEFAULT NULL COMMENT '扫描类型',
  `ctime` int(10) DEFAULT NULL COMMENT '扫描时间',
  `qrcode_id` int(10) DEFAULT NULL COMMENT '二维码ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_mp_score_record`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_score_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(255) NOT NULL COMMENT '粉丝openid',
  `type` varchar(50) DEFAULT 'score' COMMENT '积分类型，socre、money等',
  `source` varchar(50) DEFAULT 'system' COMMENT '积分来源，system，addon',
  `value` int(10) NOT NULL COMMENT '积分值',
  `flag` varchar(50) DEFAULT NULL COMMENT '标识，fans_bind，IdouChat',
  `remark` text COMMENT '积分说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分记录表';


-- -----------------------------
-- 表结构 `qinfo_mp_setting`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mp_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号ID',
  `name` varchar(255) NOT NULL COMMENT '配置项',
  `value` text COMMENT '配置值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号配置';

