
-- -----------------------------
-- 表结构 `qinfo_agent`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_agent` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `status` int(4) NOT NULL DEFAULT '1',
  `level` int(4) DEFAULT '1',
  `creation_time` int(10) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- 表结构 `qinfo_customer`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_customer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `status` int(4) NOT NULL DEFAULT '1',
  `level` int(4) DEFAULT '1',
  `creation_time` int(10) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- 表结构 `qinfo_license`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_license` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cdkey` varchar(255) DEFAULT '',
  `version_type` varchar(10) NOT NULL DEFAULT '',
  `agent_info` text,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `type` tinyint(4) DEFAULT '1',
  `creation_time` int(10) DEFAULT NULL,
  `expiration_time` int(10) DEFAULT NULL,
  `config_info` text,
  `activation_code` text,
  `use_time` int(10) DEFAULT NULL,
  `use_status` tinyint(4) DEFAULT '0',
  `use_info` text,
  `hardware_info` text,
  `end_time` int(10) DEFAULT NULL,
  `start_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- 表结构 `qinfo_package`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_package` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '序列号，一般用日期数字标示',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '版本名',
  `license_id` int(11) DEFAULT NULL,
  `url` varchar(150) DEFAULT '' COMMENT '链接到的远程文章',
  `name` varchar(20) NOT NULL DEFAULT '',
  `number` int(10) NOT NULL,
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `update_time` int(11) NOT NULL COMMENT '更新的时间',
  `module` text,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `generate` tinyint(4) NOT NULL DEFAULT '0',
  `generate_time` int(11) DEFAULT '0',
  `config` text,
  `agent_info` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

