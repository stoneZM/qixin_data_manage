CREATE TABLE IF NOT EXISTS `qinfo_vhost_info` (
  `id` int(10) DEFAULT NULL,
  `vhost_source_ip` varchar(20) NOT NULL DEFAULT '',
  `vhost_snap_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vhost_name` varchar(255) NOT NULL DEFAULT '',
  `is_active` int(10) DEFAULT '0',
  `is_normal` int(10) DEFAULT '0',
  `info` text,
  UNIQUE KEY `vhost_name` (`vhost_name`),
  UNIQUE KEY `vhost_snap_id` (`vhost_snap_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
