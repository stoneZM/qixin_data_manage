-- -----------------------------
-- 表结构 `qinfo_mount`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_mount` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `source_ip` varchar(255) DEFAULT NULL,
  `snap_id` int(10) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '0未开始,1进行中,2已完成,3失败',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='挂载表';

