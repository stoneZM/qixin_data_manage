-- -----------------------------
-- 表结构 `qinfo_remote_auth`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_remote_auth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(40) NOT NULL,
  `passwd` varchar(40) NOT NULL,
  `ip` varchar(20) DEFAULT ''' ''',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_remote_device`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_remote_device` (
  `unique_id` varchar(20) COLLATE utf8mb4_bin NOT NULL DEFAULT '0',
  `ip` varchar(20) COLLATE utf8mb4_bin DEFAULT '0.0.0.0',
  `system_info` varchar(500) COLLATE utf8mb4_bin DEFAULT '',
  `alias` varchar(100) COLLATE utf8mb4_bin DEFAULT '',
  `port` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `type` tinyint(1) unsigned zerofill DEFAULT '0',
  `create_time` int(10) unsigned DEFAULT '0',
  `server_ip` varchar(20) COLLATE utf8mb4_bin DEFAULT '',
  `user_name` varchar(20) COLLATE utf8mb4_bin DEFAULT '',
  `passwd` varchar(30) COLLATE utf8mb4_bin DEFAULT '',
  PRIMARY KEY (`unique_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


-- -----------------------------
-- 表结构 `qinfo_remote_snap`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_remote_snap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned DEFAULT NULL,
  `task_id` int(10) DEFAULT '0',
  `sub_task_id` int(10) unsigned DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `have_os` tinyint(1) DEFAULT NULL,
  `unique_id` varchar(20) COLLATE utf8mb4_bin DEFAULT '0',
  `harddisk_id` tinyint(1) DEFAULT NULL,
  `virtual_id` int(10) DEFAULT '0',
  `file_name` varchar(200) COLLATE utf8mb4_bin DEFAULT '',
  `create_time` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


-- -----------------------------
-- 表结构 `qinfo_remote_task`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_remote_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `to` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `task_id` int(10) unsigned NOT NULL,
  `is_realtime` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-非实时，1-实时',
  `is_twice` tinyint(1) DEFAULT '0' COMMENT '1-是二次分发任务',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

