-- -----------------------------
-- 表结构 `qinfo_goods`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '标题',
  `etitle` varchar(255) NOT NULL DEFAULT '',
  `summary` text NOT NULL COMMENT '描述',
  `content` text,
  `type_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '所属分类',
  `cover_id` int(10) unsigned DEFAULT NULL COMMENT '封面',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `token` varchar(255) DEFAULT '',
  `entity` tinyint(4) NOT NULL DEFAULT '2',
  `icon` varchar(20) DEFAULT '',
  `cover_url` varchar(255) DEFAULT '',
  `is_com` tinyint(4) DEFAULT '0',
  `is_recommend` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_goods_advert`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_goods_advert` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  `cover_id` int(10) NOT NULL,
  `link` varchar(100) CHARACTER SET utf8 DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;


-- -----------------------------
-- 表结构 `qinfo_goods_feed`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_goods_feed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8 NOT NULL,
  `summary` text CHARACTER SET utf8 NOT NULL,
  `cover_id` int(10) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `is_recommend` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;


-- -----------------------------
-- 表结构 `qinfo_goods_file`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_goods_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `name` varchar(300) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` varchar(100) NOT NULL DEFAULT '' COMMENT '保存名称',
  `savepath` varchar(100) NOT NULL DEFAULT '' COMMENT '文件保存路径',
  `ext` char(5) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `location` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件保存位置',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '远程地址',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件表';


-- -----------------------------
-- 表结构 `qinfo_goods_update`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_goods_update` (
  `number` int(10) NOT NULL COMMENT '序列号，一般用日期数字标示',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '版本号',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '版本名',
  `log` text NOT NULL COMMENT '更新日志',
  `url` varchar(150) DEFAULT '' COMMENT '链接到的远程文章',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `update_time` int(11) NOT NULL COMMENT '更新的时间',
  `is_current` tinyint(4) NOT NULL,
  `file_id` int(10) DEFAULT NULL,
  `install_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `id` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_goods_version`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_goods_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `title` varchar(32) NOT NULL DEFAULT '',
  `log` text NOT NULL,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `need_buy` int(10) DEFAULT NULL,
  `create_time` int(10) NOT NULL COMMENT '密码盐值',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  `update_time` int(10) NOT NULL,
  `sql_file` int(10) DEFAULT NULL,
  `update_sql_file` int(10) DEFAULT NULL,
  `file_id` int(10) DEFAULT NULL,
  `token` varchar(32) DEFAULT '',
  `explain` text,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

