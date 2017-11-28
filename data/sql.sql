-- -----------------------------
-- QinfoCMS MySQL Data Transfer 
-- 
-- Host     : 127.0.0.1
-- Port     : 3306
-- Database : qinfo_os
-- 
-- Part : #1
-- Date : 2017-06-16 15:30:12
-- -----------------------------

SET FOREIGN_KEY_CHECKS = 0;


-- -----------------------------
-- Table structure for `qinfo_action`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_action`;
CREATE TABLE `qinfo_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text COMMENT '行为规则',
  `log` text COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `module` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统行为表';

-- -----------------------------
-- Records of `qinfo_action`
-- -----------------------------
INSERT INTO qinfo_action VALUES ('1', 'user_login', '用户登录', '积分+10，每天一次', 'table:member|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;', '[user|get_nickname]在[time|time_format]登录了后台', '1', '1', '1387181220', 'admin');
INSERT INTO qinfo_action VALUES ('2', 'update_config', '更新配置', '新增或修改或删除配置', '', '[user|get_nickname]在[time|time_format]更新了配置', '1', '1', '1383294988', 'admin');
INSERT INTO qinfo_action VALUES ('3', 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', '1', '1', '1383296392', 'admin');
INSERT INTO qinfo_action VALUES ('4', 'add_device', '新增设备', '新增设备', '', '[user|get_nickname]在[time|time_format]新增[device_ip]设备', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('5', 'edit_device', '更新设备', '修改设备分组、别名', '', '[user|get_nickname]在[time|time_format]更新[device_ip]配置', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('6', 'add_storage', '新增存储', '新增存储', '', '[user|get_nickname]在[time|time_format]新增[storage_name]存储', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('7', 'edit_storage', '更新存储', '更新存储别名、端口、IP', '', '[user|get_nickname]在[time|time_format]更新[storage_name]存储配置', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('8', 'del_storage', '删除存储', '删除存储', '', '[user|get_nickname]在[time|time_format]删除[storage_name]存储', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('9', 'add_storage_space', '新增存储空间', '新增存储空间', '', '[user|get_nickname]在[time|time_format]新增[storage_name]存储空间[storage_space_name]', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('11', 'del_storage_space', '删除存储空间', '删除存储空间', '', '[user|get_nickname]在[time|time_format]删除[storage_name]存储空间【[storage_space_name]】', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('12', 'status_storage', '更新存储状态', '更新存储状态', '', '[user|get_nickname]在[time|time_format]更新[storage_name]存储状态', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('13', 'status_storage_space', '更新存储空间状态', '更新存储空间状态', '', '[user|get_nickname]在[time|time_format]更新[storage_name]存储空间【[storage_space_name]】的状态', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('14', 'add_compute', '新增计算池', '新增计算池', '', '[user|get_nickname]在[time|time_format]新增[compute_ip]计算池', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('15', 'edit_compute', '修改计算池', '修改计算池', '', '[user|get_nickname]在[time|time_format]修改[compute_ip]计算池配置', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('16', 'del_compute', '删除计算池', '删除计算池', '', '[user|get_nickname]在[time|time_format]删除[compute_ip]计算池配置', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('17', 'update_compute', '更新计算池', '更新计算池', '', '[user|get_nickname]在[time|time_format]更新[compute_ip]计算池配置', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('18', 'status_compute', '更新计算池状态', '更新计算池状态', '', '[user|get_nickname]在[time|time_format]更新[compute_ip]计算池状态', '1', '1', '1383296392', 'device');
INSERT INTO qinfo_action VALUES ('19', 'add_cdp_task', '新增cdp任务', '新增cdp任务', '', '[user|get_nickname]在[time|time_format]新增cdp任务', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('20', 'edit_cdp_task', '修改cdp任务', '修改cdp任务', '', '[user|get_nickname]在[time|time_format]修改了[task_time|time_format]的cdp任务，修改结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('21', 'start_cdp_task', '启动cdp任务', '启动cdp任务', '', '[user|get_nickname]在[time|time_format]启动了[task_time|time_format]的cdp任务，启动结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('22', 'pause_cdp_task', '暂停cdp任务', '暂停cdp任务', '', '[user|get_nickname]在[time|time_format]暂停了[task_time|time_format]的cdp任务，暂停结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('23', 'stop_cdp_task', '终止cdp任务', '终止cdp任务', '', '[user|get_nickname]在[time|time_format]终止了[task_time|time_format]的cdp任务，终止结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('24', 'create_snap', '创建快照', '创建快照', '', '[user|get_nickname]于[time|time_format]在[task_time]的cdp任务中创建快照，创建结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296393', 'cdp');
INSERT INTO qinfo_action VALUES ('25', 'del_snap', '删除快照', '删除快照', '', '[user|get_nickname]于[time|time_format]在[task_time]的cdp任务中删除[begin_time]到[end_time]之间的快照，删除结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296393', 'cdp');
INSERT INTO qinfo_action VALUES ('26', 'del_cdp_task', '删除cdp任务', '删除cdp任务', '', '[user|get_nickname]在 [time|time_format] 删除了[task_time|time_format]的cdp任务，删除结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('27', 'add_auto_takeover_config', '新增自动化接管配置', '新增自动化接管配置', '', '[user|get_nickname]在 [time|time_format] 新增了自动化【[type]】配置，新增结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('28', 'edit_auto_takeover_config', '新增自动化接管配置', '新增自动化接管配置', '', '[user|get_nickname]在 [time|time_format] 修改了自动化【[type]】配置，修改结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('29', 'add_auto_practise_config', '新增演练审计配置', '新增演练审计配置', '', '[user|get_nickname] [time|time_format] 在【[ip]】设备上新增了【审计演练】配置，新增结果：<strong><snap style="color:[color]">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'practise');
INSERT INTO qinfo_action VALUES ('30', 'edit_auto_practise_config', '修改演练审计配置', '修改演练审计配置', '', '[user|get_nickname]在 [time|time_format] 修改了【[ip]】设备的【演练审计】配置，修改结果：<strong><snap style="color:[color]">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'practise');
INSERT INTO qinfo_action VALUES ('31', 'add_virtual', '新增虚拟机', '新增虚拟机', '', '[user|get_nickname]在 [time|time_format] 新增了【[virtual_name]】【[virtual_type]】虚拟机，新增结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('32', 'del_virtual', '删除虚拟机', '删除虚拟机', '', '[user|get_nickname]在 [time|time_format] 删除了【[virtual_name]】虚拟机，删除结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('33', 'edit_virtual', '修改虚拟机', '修改虚拟机', '', '[user|get_nickname]在 [time|time_format] 修改了【[virtual_name]】虚拟机的【[edit_cnt]】，修改结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('34', 'stop_virtual', '关闭虚拟机', '关闭虚拟机', '', '[user|get_nickname]在 [time|time_format] 关闭了【[virtual_name]】虚拟机，关闭结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('35', 'start_virtual', '开启虚拟机', '开启虚拟机', '', '[user|get_nickname]在 [time|time_format] 开启了【[virtual_name]】虚拟机，开启结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296392', 'cdp');
INSERT INTO qinfo_action VALUES ('36', 'add_keli', '创建颗粒', '创建颗粒', '', '[user|get_nickname]于[time|time_format]在[task_time]的cdp任务中创建颗粒，创建结果：<strong><snap style=\"color:[color]\">[result]</strong><br/>[desc]', '1', '1', '1383296393', 'cdp');
INSERT INTO qinfo_action VALUES ('37', 'add_cdp_device', '新增cdp设备', '新增cdp设备', '', '[user|get_nickname]在[time|time_format]新增【[ip]】设备，新增结果：<strong><snap style="color:[color]">[result]</strong><br/>[desc]', '1', '1', '1383296393', 'cdp');
INSERT INTO qinfo_action VALUES ('38', 'del_cdp_device', '删除cdp设备', '删除cdp设备', '', '[user|get_nickname]在[time|time_format]删除【[ip]】设备，删除结果：<strong><snap style="color:[color]">[result]</strong><br/>[desc]', '1', '1', '1383296393', 'cdp');


-- -----------------------------
-- Table structure for `qinfo_action_limit`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_action_limit`;
CREATE TABLE `qinfo_action_limit` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `frequency` int(10) NOT NULL,
  `time_number` int(10) NOT NULL,
  `time_unit` varchar(50) NOT NULL,
  `punish` text NOT NULL,
  `if_message` tinyint(4) NOT NULL,
  `message_content` text NOT NULL,
  `action_list` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  `create_time` int(10) NOT NULL,
  `module` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------
-- Table structure for `qinfo_action_log`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_action_log`;
CREATE TABLE `qinfo_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  `extended_data` text,
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表';


-- -----------------------------
-- Table structure for `qinfo_addons`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_addons`;
CREATE TABLE `qinfo_addons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `isinstall` int(10) DEFAULT '0' COMMENT '是否安装',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='插件表';

-- -----------------------------
-- Records of `qinfo_addons`
-- -----------------------------
INSERT INTO `qinfo_addons` VALUES ('1', 'Devteam', '开发团队信息', '开发团队成员信息', '1', '', 'molong', '0.1', '1', '0', '0');
INSERT INTO `qinfo_addons` VALUES ('2', 'Sitestat', '站点统计信息', '统计站点的基础信息', '1', '', 'thinkphp', '0.2', '1', '0', '0');
INSERT INTO `qinfo_addons` VALUES ('3', 'Systeminfo', '系统环境信息', '用于显示一些服务器的信息', '1', '', 'molong', '0.1', '1', '0', '0');
INSERT INTO `qinfo_addons` VALUES ('4', 'Syslogin', '第三方登录', '第三方登录', '0', '', 'molong', '0.1', '0', '1478238288', '0');
INSERT INTO `qinfo_addons` VALUES ('5', 'Saleinfo', '软件销售信息', '用于显示销售的信息', '1', '', 'sundawei', '0.1', '1', '1479710452', '0');
INSERT INTO `qinfo_addons` VALUES ('6', 'Monitor', '服务器监控', '服务器监控基础信息', '1', '', 'sundawei', '0.1', '1', '1498784401', '0');

-- -----------------------------
-- Table structure for `qinfo_attachment`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_attachment`;
CREATE TABLE `qinfo_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '附件显示名',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型',
  `source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源ID',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '附件大小',
  `dir` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '上级目录ID',
  `sort` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `idx_record_status` (`record_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';


-- -----------------------------
-- Table structure for `qinfo_auth_extend`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_auth_extend`;
CREATE TABLE `qinfo_auth_extend` (
  `group_id` mediumint(10) unsigned NOT NULL COMMENT '用户id',
  `extend_id` mediumint(8) unsigned NOT NULL COMMENT '扩展表中数据的id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '扩展类型标识 1:栏目分类权限;2:模型权限',
  UNIQUE KEY `group_extend_type` (`group_id`,`extend_id`,`type`),
  KEY `uid` (`group_id`),
  KEY `group_id` (`extend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组与分类的对应关系表';


-- -----------------------------
-- Table structure for `qinfo_auth_group`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_auth_group`;
CREATE TABLE `qinfo_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` text NOT NULL COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_auth_group`
-- -----------------------------
INSERT INTO `qinfo_auth_group` VALUES ('1', '系统管理员', '系统管理员', '1', '1,15,2,12,21,25,26,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,75,76,77,78,79,7,8,10,11,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,159,160,161');
INSERT INTO `qinfo_auth_group` VALUES ('2', '安全管理员', '设置用户权限', '1', '1,15,7,8,30,31,32,33,34,45,46,157,158');
INSERT INTO `qinfo_auth_group` VALUES ('3', '审计管理员', '设置用户行为，日志查看', '1', '15,7,10,11,39,40,41,42,43,44,45,46,143,144,157');
INSERT INTO `qinfo_auth_group` VALUES ('4', '用户管理员', '新增、删除用户', '1', '1,15,7,27,28,29,45,46,157');
INSERT INTO `qinfo_auth_group` VALUES ('5', '操作员', '功能应用', '1', '1,15,45,46,80,84,85,86,87,88,89,90,91,92,93,94,109,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,157');

-- -----------------------------
-- Table structure for `qinfo_auth_group_access`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_auth_group_access`;
CREATE TABLE `qinfo_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_auth_group_access`
-- -----------------------------
INSERT INTO `qinfo_auth_group_access` VALUES ('2', '1');
INSERT INTO `qinfo_auth_group_access` VALUES ('3', '2');
INSERT INTO `qinfo_auth_group_access` VALUES ('4', '4');
INSERT INTO `qinfo_auth_group_access` VALUES ('5', '3');

-- -----------------------------
-- Table structure for `qinfo_auth_rule`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_auth_rule`;
CREATE TABLE `qinfo_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `group` char(20) NOT NULL DEFAULT '' COMMENT '权限节点分组',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_auth_rule`
-- -----------------------------
INSERT INTO `qinfo_auth_rule` VALUES ('1', 'admin', '2', 'admin/index/clear', 'update_cache', 'home', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('2', 'admin', '2', 'admin/config/group', 'basic_config', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('3', 'admin', '2', 'admin/menu/index', 'menu_manage', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('4', 'admin', '2', 'admin/server/index', 'service_install', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('5', 'admin', '2', 'admin/menu/import', 'import_menu', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('6', 'admin', '2', 'admin/seo/index', 'seo_settings', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('7', 'admin', '2', 'admin/user/index', 'user_list', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('8', 'admin', '2', 'admin/group/index', 'user_group', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('9', 'admin', '2', 'admin/group/access', 'auth_list', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('10', 'admin', '2', 'admin/action/index', 'user_behavior', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('11', 'admin', '2', 'admin/action/log', 'behavior_log', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('12', 'admin', '2', 'admin/addons/index', 'plugin_list', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('13', 'admin', '2', 'admin/addons/hooks', 'hook_list', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('14', 'admin', '2', 'admin/seo/rewrite', 'routing_rules', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('15', 'admin', '2', 'admin/index/index', 'console', 'home', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('16', 'admin', '2', 'admin/config/index', 'advanced_config', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('17', 'admin', '2', 'admin/config/add', 'add_config', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('18', 'admin', '2', 'admin/config/edit', 'edit_config', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('19', 'admin', '2', 'admin/menu/add', 'add_menu', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('20', 'admin', '2', 'admin/menu/edit', 'edit_menu', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('21', 'admin', '2', 'admin/module/index', 'module_list', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('22', 'admin', '2', 'admin/database/dataimport', 'data_recovery', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('23', 'admin', '2', 'admin/database/index', 'data_backup', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('24', 'admin', '2', 'admin/software/index', 'software_download', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('25', 'admin', '2', 'admin/cloud/index', 'cloud_market', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('26', 'admin', '2', 'admin/cloud/update', 'auto_update', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('27', 'admin', '2', 'admin/user/add', 'add_user', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('28', 'admin', '2', 'admin/user/edit', 'edit_user', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('29', 'admin', '2', 'admin/user/del', 'del_user', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('30', 'admin', '2', 'admin/user/auth', 'auth_user', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('31', 'admin', '2', 'admin/group/add', 'add_user_group', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('32', 'admin', '2', 'admin/group/edit', 'edit_user_group', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('33', 'admin', '2', 'admin/group/auth', 'auth_user_group', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('34', 'admin', '2', 'admin/group/del', 'del_user_group', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('35', 'admin', '2', 'admin/group/addnode', 'add_user_node', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('36', 'admin', '2', 'admin/group/editnode', 'edit_user_node', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('37', 'admin', '2', 'admin/group/delnode', 'del_user_node', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('38', 'admin', '2', 'admin/group/upnode', 'up_user_node', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('39', 'admin', '2', 'admin/action/add', 'add_action', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('40', 'admin', '2', 'admin/action/edit', 'edit_action', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('41', 'admin', '2', 'admin/action/del', 'del_action', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('42', 'admin', '2', 'admin/action/setstatus', 'status_action', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('43', 'admin', '2', 'admin/action/dellog', 'del_action_log', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('44', 'admin', '2', 'admin/action/clear', 'del_action_clear', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('45', 'admin', '2', 'admin/user/editpwd', 'edit_user_pass', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('46', 'admin', '2', 'admin/user/avatar', 'edit_user_avatar', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('47', 'admin', '2', 'admin/server/add', 'add_server', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('48', 'admin', '2', 'admin/server/edit', 'edit_server', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('49', 'admin', '2', 'admin/server/del', 'del_server', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('50', 'admin', '2', 'admin/server/ssh2', 'ssh2_server', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('51', 'admin', '2', 'admin/server/sendcmd', 'sendcmd_server', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('52', 'admin', '2', 'admin/server/fastcmd', 'fastcmd_server', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('53', 'admin', '2', 'admin/server/command', 'command', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('54', 'admin', '2', 'admin/server/command_add', 'add_command', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('55', 'admin', '2', 'admin/server/command_edit', 'edit_command', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('56', 'admin', '2', 'admin/server/command_del', 'del_command', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('57', 'admin', '2', 'admin/server/link', 'link_server', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('58', 'admin', '2', 'admin/software/download_state', 'download_state', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('59', 'admin', '2', 'admin/software/download_file', 'download_file', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('60', 'admin', '2', 'admin/cloud/version', 'cloud_look_version', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('61', 'admin', '2', 'admin/cloud/install', 'cloud_install', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('62', 'admin', '2', 'admin/module/edit', 'edit_module', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('63', 'admin', '2', 'admin/module/uninstall', 'uninstall_module', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('64', 'admin', '2', 'admin/module/install', 'install_module', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('65', 'admin', '2', 'admin/addons/config', 'config_addons', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('66', 'admin', '2', 'admin/addons/uninstall', 'uninstall_addons', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('67', 'admin', '2', 'admin/addons/disable', 'disable_addons', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('68', 'admin', '2', 'admin/addons/enable', 'enable_addons', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('69', 'admin', '2', 'admin/addons/install', 'install_addons', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('70', 'admin', '2', 'admin/database/export', 'export_database', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('71', 'admin', '2', 'admin/database/optimize', 'optimize_database', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('72', 'admin', '2', 'admin/database/repair', 'repair_database', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('73', 'admin', '2', 'admin/database/del', 'del_database', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('74', 'admin', '2', 'admin/database/import', 'import_database', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('75', 'admin', '2', 'admin/cloud/getfilelist', 'cloud_getfilelist', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('76', 'admin', '2', 'admin/cloud/compare', 'cloud_compare', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('77', 'admin', '2', 'admin/cloud/cover', 'cloud_cover', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('78', 'admin', '2', 'admin/cloud/updb', 'cloud_updb', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('79', 'admin', '2', 'admin/cloud/finish', 'cloud_finish', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('80', 'device', '2', 'device/manage/device', 'device_manage', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('81', 'device', '2', 'device/manage/storage', 'storage_pool', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('82', 'device', '2', 'device/manage/computenode', 'compute_pool', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('83', 'device', '2', 'device/manage/device_group', 'device_group', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('84', 'device', '2', 'device/manage/get_device_lists', 'get_device_lists', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('85', 'device', '2', 'device/manage/device_add', 'add_device', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('86', 'device', '2', 'device/manage/get_device', 'get_device', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('87', 'device', '2', 'device/manage/device_edit', 'edit_device', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('88', 'device', '2', 'device/manage/device_del', 'del_device', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('89', 'device', '2', 'device/manage/device_detail', 'detail_device', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('90', 'device', '2', 'device/manage/get_device_monitor', 'get_device_monitor', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('91', 'device', '2', 'device/manage/soft_status', 'soft_status', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('92', 'device', '2', 'device/manage/update', 'soft_update', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('93', 'device', '2', 'device/manage/uninstall', 'soft_uninstall', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('94', 'device', '2', 'device/manage/soft_restart', 'soft_restart', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('95', 'device', '2', 'device/manage/group_add', 'add_device_group', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('96', 'device', '2', 'device/manage/group_edit', 'edit_device_group', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('97', 'device', '2', 'device/manage/group_del', 'del_device_group', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('98', 'device', '2', 'device/manage/snmp_edit', 'edit_device_snmp', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('99', 'device', '2', 'device/manage/storage_add', 'add_storage', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('100', 'device', '2', 'device/manage/storage_manage', 'storage_manage', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('101', 'device', '2', 'device/manage/storage_edit', 'edit_storage', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('102', 'device', '2', 'device/manage/storage_status', 'storage_status', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('103', 'device', '2', 'device/manage/storage_del', 'del_storage', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('104', 'device', '2', 'device/manage/storage_space_add', 'add_storage_space', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('105', 'device', '2', 'device/manage/storage_space_status', 'status_storage_space', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('106', 'device', '2', 'device/manage/storage_space_del', 'del_storage_space', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('107', 'device', '2', 'device/manage/compute_add', 'add_compute', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('108', 'device', '2', 'device/manage/compute_edit', 'edit_compute', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('109', 'device', '2', 'device/manage/compute_detail', 'detail_compute', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('110', 'device', '2', 'device/manage/compute_status', 'status_compute', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('111', 'device', '2', 'device/manage/vnc', 'vnc_compute', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('112', 'device', '2', 'device/manage/virtual_detail', 'virtual_detail', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('113', 'device', '2', 'device/manage/virtual_del', 'del_virtual', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('114', 'device', '2', 'device/manage/virtual_edit_type', 'edit_type_virtual', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('115', 'device', '2', 'device/manage/virtual_edit', 'edit_virtual', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('116', 'device', '2', 'device/manage/add_disk', 'add_disk_virtual', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('117', 'device', '2', 'device/manage/virtual_status', 'status_virtual', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('118', 'device', '2', 'device/manage/add_network', 'add_network_virtual', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('119', 'device', '2', 'device/manage/virtual', 'virtual_list', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('120', 'cdp', '2', 'cdp/index/index', 'cdp_manage', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('121', 'cdp', '2', 'cdp/index/add_device', 'addcdp_device', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('122', 'cdp', '2', 'cdp/index/del_device', 'delcdp_device', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('123', 'cdp', '2', 'cdp/index/detail', 'detail_cdp', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('124', 'cdp', '2', 'cdp/index/task_add', 'addcdp_task', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('125', 'cdp', '2', 'cdp/index/get_task_snap', 'getcdp_task_snap', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('126', 'cdp', '2', 'cdp/index/get_clone_state', 'getcdp_clone_state', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('127', 'cdp', '2', 'cdp/index/task_status', 'cdp_task_status', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('128', 'cdp', '2', 'cdp/index/create_snap', 'cdp_create_snap', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('129', 'cdp', '2', 'cdp/index/merge_snap', 'cdp_merge_snap', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('130', 'cdp', '2', 'cdp/index/del_task', 'cdp_del_task', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('131', 'cdp', '2', 'cdp/index/snap_detail', 'cdp_snap_detail', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('132', 'cdp', '2', 'cdp/index/auto_conf', 'cdp_auto_conf', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('133', 'cdp', '2', 'cdp/index/edit_auto_config', 'edit_auto_config', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('134', 'cdp', '2', 'cdp/index/add_auto_config', 'add_auto_config', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('135', 'cdp', '2', 'cdp/index/add_virtual', 'add_cdp_virtual', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('136', 'cdp', '2', 'device/manage/generate_mac_addr', 'generate_mac_addr', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('137', 'cdp', '2', 'cdp/index/add_keli', 'add_cdp_keli', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('138', 'cdp', '2', 'cdp/index/get_task', 'get_cdp_task', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('139', 'cdp', '2', 'cdp/index/task_edit', 'edit_cdp_task', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('140', 'cdp', '2', 'cdp/index/snap_del', 'delete_cdp_snap', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('141', 'cdp', '2', 'cdp/index/get_log', 'get_log', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('142', 'admin', '2', 'admin/license/index', 'license', 'home', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('143', 'admin', '2', 'admin/license/license', 'auth_activation', 'home', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('144', 'device', '2', 'device/manage/compute_del', 'del_compute', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('145', 'device', '2', 'device/manage/update_compute', 'update_compute', 'device', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('146', 'admin', '2', 'admin/action/detail', 'action_detail', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('147', 'admin', '2', 'admin/upload/upload', 'file_upload', 'home', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('148', 'admin', '2', 'admin/upload/delete', 'file_upload_del', 'home', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('149', 'admin', '2', 'admin/module/import_module', 'import_module', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('150', 'admin', '2', 'admin/cloud/getversionlist', 'cloud_getversion', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('151', 'admin', '2', 'admin/cloud/updategoods', 'cloud_updategoods', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('152', 'admin', '2', 'admin/cloud/updating1', 'cloud_updating1', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('153', 'admin', '2', 'admin/cloud/updating2', 'cloud_updating2', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('154', 'admin', '2', 'admin/cloud/updating3', 'cloud_updating3', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('155', 'admin', '2', 'admin/cloud/updating4', 'cloud_updating4', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('156', 'admin', '2', 'admin/cloud/updating5', 'cloud_updating5', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('157', 'admin', '2', 'admin/module/del', 'del_module', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('158', 'admin', '2', 'admin/module/manual_upgrade', 'manual_upgrade', 'system', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('159', 'admin', '2', 'admin/user/updata_avatar', 'updata_avatar', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('160', 'admin', '2', 'admin/user/auth_device', 'auth_device', 'user', '1', '');
INSERT INTO `qinfo_auth_rule` VALUES ('161', 'admin', '2', 'admin/log/index', 'log_report', 'home', '1', '');


-- -----------------------------
-- Table structure for `qinfo_cdp`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_cdp`;
CREATE TABLE `qinfo_cdp` (
  `device_id` int(10) NOT NULL,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `status` tinyint(4) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- Table structure for `qinfo_cdp_config`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_cdp_config`;
CREATE TABLE `qinfo_cdp_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `device_id` int(10) unsigned DEFAULT '0',
  `compute_id` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) DEFAULT '' COMMENT '用户名',
  `password` varchar(50) DEFAULT '' COMMENT '密码',
  `day` varchar(20)  NOT NULL DEFAULT '',
  `accurate_time` int(10) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '接管-0  演练-1',
  `net_data` text,
  `cpu` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `memory` int(5) unsigned NOT NULL DEFAULT '1024',
  `system_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'window-1  linux-0',
  `status` tinyint(1) DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- Table structure for `qinfo_cdp_keli`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_cdp_keli`;
CREATE TABLE `qinfo_cdp_keli` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL COMMENT '快照的组id',
  `task_id` int(10) unsigned NOT NULL COMMENT '任务id',
  `virtual_id` int(10) DEFAULT NULL,
  `keli_time` int(10) unsigned DEFAULT NULL COMMENT '颗粒所在的时间戳',
  `create_time` varchar(15) NOT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '颗粒的状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- Table structure for `qinfo_cdp_snap`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_cdp_snap`;
CREATE TABLE `qinfo_cdp_snap` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned DEFAULT '0',
  `type` tinyint(4) DEFAULT '0',
  `task_id` int(10) DEFAULT '0',
  `harddisk_id` int(10) DEFAULT '0',
  `storage_id` varchar(10) DEFAULT '0',
  `compute_id` int(10) DEFAULT '0',
  `virtual_id` int(10) DEFAULT '0',
  `keli_id` int(10) unsigned DEFAULT '0',
  `file_name` varchar(255) DEFAULT '',
  `file_path` varchar(255) DEFAULT '',
  `have_os` tinyint(4) DEFAULT '0',
  `data_size` bigint(20) DEFAULT '0',
  `create_time` int(10) DEFAULT '0',
  `update_time` int(10) DEFAULT '0',
  `backup_partition` text,
  `parent_id` int(10) DEFAULT '0',
  `sub_task_id` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- Table structure for `qinfo_cdp_task`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_cdp_task`;
CREATE TABLE `qinfo_cdp_task` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `device_id` int(10) DEFAULT '0',
  `device_unique_id` varchar(255) DEFAULT '',
  `status` tinyint(4) DEFAULT '0',
  `type` varchar(20) DEFAULT '',
  `engine_status` tinyint(4) DEFAULT '0',
  `create_time` int(10) DEFAULT '0',
  `system_desc` varchar(255) DEFAULT '',
  `synchr_time` int(10) DEFAULT '0',
  `stop_time` varchar(255) DEFAULT '',
  `backup_speed` int(10) DEFAULT '0',
  `snap_num` int(10) DEFAULT '0',
  `snap_time` int(10) DEFAULT '0',
  `sub_snap_num` int(10) DEFAULT '0',
  `storage_id` varchar(255) DEFAULT '',
  `snappoint_path` varchar(255) DEFAULT '',
  `harddisk_info` text,
  `work_partition` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- Table structure for `qinfo_compute`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_compute`;
CREATE TABLE `qinfo_compute` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `ip` varchar(20) DEFAULT '',
  `port` int(10) DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `creation_time` int(10) DEFAULT '0',
  `is_primary` tinyint(4) DEFAULT '0',
  `compute_info` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- Records of `qinfo_compute`
-- -----------------------------
INSERT INTO `qinfo_compute` VALUES ('1', 'localhost', '127.0.0.1', '80', '1', '1474961317', '1', '');

-- -----------------------------
-- Table structure for `qinfo_compute_virtual`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_compute_virtual`;
CREATE TABLE `qinfo_compute_virtual` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `snap_id` varchar(20) DEFAULT NULL,
  `device_id` int(10) DEFAULT '0',
  `task_id` int(10) DEFAULT '0',
  `name` varchar(255) DEFAULT ' ',
  `status` int(10) DEFAULT '0',
  `type` int(10) DEFAULT '0',
  `system_type` int(10) DEFAULT '0',
  `system_bit` int(10) DEFAULT '0',
  `source_device_name` varchar(255) DEFAULT ' ',
  `source_device_ip` varchar(255) DEFAULT ' ',
  `task_uuid` varchar(255) DEFAULT ' ',
  `cpu_kernel` int(10) DEFAULT '0',
  `memory` int(10) DEFAULT '0',
  `vmx_path` varchar(255) DEFAULT '',
  `comput_id` int(10) NOT NULL DEFAULT '1',
  `mount_hd_id` int(10) DEFAULT '0',
  `usb_controller` int(10) DEFAULT '0',
  `usb_high_speed` int(10) DEFAULT '0',
  `usb_new_devices` int(10) DEFAULT '0',
  `usb_all_devices` int(10) DEFAULT '0',
  `scsi_state_connected` int(10) DEFAULT '0',
  `scsi_state_power_on` int(10) DEFAULT '0',
  `scsi_connection_to` int(10) DEFAULT '0',
  `scsi_device_node` int(10) DEFAULT '0',
  `ace_enable` int(10) DEFAULT '0',
  `ace_user_server` varchar(255) DEFAULT ' ',
  `ace_server` int(10) DEFAULT '0',
  `ace_port` int(10) DEFAULT '0',
  `remote_display` int(10) DEFAULT '0',
  `remote_port` int(10) DEFAULT '0',
  `remote_pwd` varchar(255) DEFAULT '0',
  `virtual_info` text,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- Table structure for `qinfo_config`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_config`;
CREATE TABLE `qinfo_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` varchar(10) NOT NULL DEFAULT 'text' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '配置说明',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '小图标',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_config`
-- -----------------------------
INSERT INTO `qinfo_config` VALUES ('1', 'config_group_list', 'textarea', '配置分组', '99', '', '', '', '1447305542', '1452323143', '1', '1:basic\r\n3:email\r\n4:service\r\n99:system', '0');
INSERT INTO `qinfo_config` VALUES ('2', 'hooks_type', 'textarea', '钩子的类型', '99', '', '类型 1-用于扩展显示内容，2-用于扩展业务处理', '', '1379313397', '1379313407', '1', '1:view\r\n2:controller', '6');
INSERT INTO `qinfo_config` VALUES ('3', 'auth_config', 'textarea', 'Auth配置', '99', '', '自定义Auth.class.php类配置', '', '1379409310', '1379409564', '1', 'AUTH_ON:1\r\nAUTH_TYPE:2', '8');
INSERT INTO `qinfo_config` VALUES ('5', 'data_backup_path', 'text', '数据库备份根路径', '99', '', '路径必须以 / 结尾', '', '1381482411', '1381482411', '1', './data/backup/', '5');
INSERT INTO `qinfo_config` VALUES ('6', 'data_backup_part_size', 'text', '数据库备份卷大小', '99', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '', '1381482488', '1381729564', '1', '20971520', '7');
INSERT INTO `qinfo_config` VALUES ('7', 'data_backup_compress', 'bool', '启用数据库备份文件压缩', '99', '0:disable\r\n1:enable', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', '', '1381713345', '1483939236', '1', '1', '9');
INSERT INTO `qinfo_config` VALUES ('8', 'data_backup_compress_level', 'select', '数据库备份文件压缩级别', '99', '1:ordinary\r\n4:commonly\r\n9:highest', '数据库备份文件的压缩级别，该配置在开启压缩时生效', '', '1381713408', '1483939357', '1', '9', '10');
INSERT INTO `qinfo_config` VALUES ('9', 'develop_mode', 'bool', '开启开发者模式', '99', '0:close\r\n1:open', '是否开启开发者模式', '', '1383105995', '1483938431', '1', '1', '11');
INSERT INTO `qinfo_config` VALUES ('10', 'allow_visit', 'textarea', '不受限控制器方法', '99', '', '', '', '1386644047', '1438075615', '1', '0:article/draftbox\r\n1:article/mydocument\r\n2:Category/tree\r\n3:Index/verify\r\n4:file/upload\r\n5:file/download\r\n6:user/updatePassword\r\n7:user/updateNickname\r\n8:user/submitPassword\r\n9:user/submitNickname\r\n10:file/uploadpicture', '0');
INSERT INTO `qinfo_config` VALUES ('11', 'deny_visit', 'textarea', '超管专限控制器方法', '99', '', '仅超级管理员可访问的控制器方法', '', '1386644141', '1438075628', '1', '0:Addons/addhook\r\n1:Addons/edithook\r\n2:Addons/delhook\r\n3:Addons/updateHook\r\n4:Admin/getMenus\r\n5:Admin/recordList\r\n6:AuthManager/updateRules\r\n7:AuthManager/tree', '0');
INSERT INTO `qinfo_config` VALUES ('12', 'admin_allow_ip', 'text', '后台允许访问IP', '99', '', '多个用逗号分隔，如果不配置表示不限制IP访问', '', '1387165454', '1452307198', '1', '', '12');
INSERT INTO `qinfo_config` VALUES ('13', 'web_site_close', 'bool', '关闭站点', '1', '0:no,1:yes', '站点关闭后其他用户不能访问，管理员可以正常访问', '', '1378898976', '1483938626', '1', '0', '4');
INSERT INTO `qinfo_config` VALUES ('14', 'web_site_icp', 'text', '网站备案号', '1', '', '设置在网站底部显示的备案号，如“赣ICP备13006622号', '', '1378900335', '1379235859', '1', '沪ICP备16033232号', '7');
INSERT INTO `qinfo_config` VALUES ('15', 'list_rows', 'num', '列表条数', '99', '', '', '', '1448937662', '1448937662', '1', '20', '10');
INSERT INTO `qinfo_config` VALUES ('16', 'config_type_list', 'textarea', '字段类型', '99', '', '', '', '1459136529', '1459136529', '1', 'text:fieldtype_text:varchar\r\nstring:fieldtype_string:int\r\npassword:fieldtype_password:varchar\r\ntextarea:fieldtype_textarea:text\r\nbool:fieldtype_bool:int\r\nselect:fieldtype_select:varchar\r\nnum:fieldtype_num:int\r\ndecimal:fieldtype_decimal:decimal\r\ntags:fieldtype_tags:varchar\r\ndatetime:fieldtype_datetime:int\r\ndate:fieldtype_date:varchar\r\neditor:fieldtype_editor:text\r\nweditor:fieldtype_weditor:text\r\nbind:fieldtype_bind:int\r\nimage:fieldtype_image:int\r\nimages:fieldtype_images:varchar\r\nattach:fieldtype_attach:varchar', '0');
INSERT INTO `qinfo_config` VALUES ('17', 'document_position', 'textarea', '文档推荐位', '99', '', '', '', '1453449698', '1453449698', '1', '1:首页推荐\r\n2:列表推荐', '0');
INSERT INTO `qinfo_config` VALUES ('18', 'mail_host', 'text', 'smtp服务器的名称', '3', '', 'smtp服务器的名称', '', '1455690530', '1455690556', '1', 'smtp.163.com', '0');
INSERT INTO `qinfo_config` VALUES ('19', 'mail_smtpauth', 'select', '启用smtp认证', '3', '0:no,1:yes', '启用smtp认证', '', '1455690641', '1483938845', '1', '1', '0');
INSERT INTO `qinfo_config` VALUES ('20', 'mail_username', 'text', '邮件发送用户名', '3', '', '邮件发送用户名', '', '1455690771', '1455690771', '1', '', '0');
INSERT INTO `qinfo_config` VALUES ('21', 'mail_password', 'text', '邮箱密码', '3', '', '邮箱密码，如果是qq邮箱，则填安全密码', '', '1455690802', '1480993892', '1', '', '0');
INSERT INTO `qinfo_config` VALUES ('22', 'mail_fromname', 'text', '发件人姓名', '3', '', '发件人姓名', '', '1455690838', '1480993884', '1', 'test', '0');
INSERT INTO `qinfo_config` VALUES ('23', 'mail_ishtml', 'select', '是否HTML格式邮件', '3', '0:no,1:yes', '是否HTML格式邮件', '', '1455690888', '1483938824', '1', '1', '0');
INSERT INTO `qinfo_config` VALUES ('24', 'mail_charset', 'text', '邮件编码', '3', '', '设置发送邮件的编码', '', '1455690920', '1455690920', '1', 'UTF8', '0');
INSERT INTO `qinfo_config` VALUES ('25', 'mail_driver', 'select', '发送类型', '3', 'fsock:fsock,phpmailer:phpmailer', '发送类型', '', '1480993305', '1480993319', '1', 'phpmailer', '0');
INSERT INTO `qinfo_config` VALUES ('26', 'default_lang', 'bool', '语言', '1', 'zh-cn:zh-cn', '', '', '1440901307', '1483938689', '1', 'zh-cn', '4');
INSERT INTO `qinfo_config` VALUES ('27', 'sevice_host', 'text', '服务器地址', '4', '', '服务器链接IP', '', '1489724478', '1489726076', '0', '127.0.0.1', '1');
INSERT INTO `qinfo_config` VALUES ('28', 'msg_host', 'text', '消息服务器', '4', '', '', '', '1489724633', '1489724633', '1', '127.0.0.1', '2');
INSERT INTO `qinfo_config` VALUES ('29', 'index_host', 'text', '索引服务器', '4', '', '', '', '1489724691', '1489724691', '0', '127.0.0.1', '4');
INSERT INTO `qinfo_config` VALUES ('30', 'vnc_host', 'text', 'vnc地址', '4', '', '', '', '1489726130', '1489726130', '1', '127.0.0.1', '6');
INSERT INTO `qinfo_config` VALUES ('31', 'vnc_web_host', 'text', 'VNC网页地址', '4', '', '', '', '1489726272', '1489726495', '1', '127.0.0.1', '7');
INSERT INTO `qinfo_config` VALUES ('32', 'vnc_web_port', 'text', 'VNC网页端口', '4', '', '', '', '1489726537', '1489726537', '1', '6080', '8');
INSERT INTO `qinfo_config` VALUES ('33', 'storage_host', 'text', '存储服务器', '4', '', '', '', '1489726631', '1489726631', '1', '127.0.0.1', '9');
INSERT INTO `qinfo_config` VALUES ('34', 'mobile_url', 'text', '微官网地址', '4', '', '', '', '1489726723', '1489726723', '1', 'm.qinfo360.cn', '10');
INSERT INTO `qinfo_config` VALUES ('35', 'wechat_appid', 'text', '微信APPID', '4', '', '', '', '1489726819', '1489726819', '1', '', '11');
INSERT INTO `qinfo_config` VALUES ('36', 'wechat_appsecret', 'text', '微信APPSECRET', '4', '', '', '', '1489726857', '1489726857', '1', '', '12');
INSERT INTO `qinfo_config` VALUES ('37', 'os_update_url', 'text', '系统更新地址', '4', '', '', '', '1489726857', '1489726857', '1', 'http://os.qinfo360.cn', '13');
INSERT INTO `qinfo_config` VALUES ('38', 'msg_port', 'text', '消息服务端口', '4', '', '', '', '1490173772', '1490173778', '1', '5000', '3');
INSERT INTO `qinfo_config` VALUES ('39', 'index_port', 'text', '索引服务端口', '4', '', '', '', '1490173772', '1490173778', '0', '5001', '5');
INSERT INTO `qinfo_config` VALUES ('40', 'system_type', 'text', '系统类型', '99', '', '', '', '1490173772', '1490173778', '1', 'base', '0');
INSERT INTO `qinfo_config` VALUES ('41', 'task_config', 'textarea', 'cdp任务的默认配置', '99', '', '', '', '1490173772', '1490173772', '1', '\"backup_speed\":0,\"snap_num\":64,\"snap_merge_time\":259200', '0');
INSERT INTO `qinfo_config` VALUES ('42', 'site_title', 'text', '默认标题', '99', '', '', '', '1378898976', '1379235274', '1', '齐信数据管理平台', '0');
INSERT INTO `qinfo_config` VALUES ('43', 'site_alias', 'text', '默认缩写', '99', '', '', '', '1378898976', '1379235274', '1', 'QIF', '0');
INSERT INTO `qinfo_config` VALUES ('44', 'site_copyright', 'text', '默认版权', '99', '', '', '', '1490173772', '1490173772', '1', 'Copyright © 2016-2017 Qinfo360.com. All rights reserved.', '0');
INSERT INTO `qinfo_config` VALUES ('45', 'site_corporate_name', 'text', '默认公司', '99', '', '', '', '1490173772', '1490173772', '1', '齐信软件科技（上海）有限公司', '0');
INSERT INTO `qinfo_config` VALUES ('46', 'site_url', 'text', '默认地址', '99', '', '', '', '1490173772', '1490173772', '1', 'www.qinfo360.cn', '0');


-- -----------------------------
-- Table structure for `qinfo_device`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_device`;
CREATE TABLE `qinfo_device` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `siteid` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) DEFAULT '',
  `ip` varchar(50) DEFAULT '',
  `unique_id` varchar(50) DEFAULT '',
  `group_id` int(10) NOT NULL DEFAULT '1',
  `type` int(10) DEFAULT '1',
  `status` tinyint(4) DEFAULT '0',
  `remark` varchar(255) DEFAULT '',
  `hardware_info` text,
  `software_info` text,
  `system_info` text,
  `harddisk_info` text,
  `app` varchar(255) DEFAULT '',
  `attribute` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`siteid`),
  UNIQUE KEY `unique_id` (`unique_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- Table structure for `qinfo_device_auth`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_device_auth`;
CREATE TABLE `qinfo_device_auth` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `device_id` text,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- Table structure for `qinfo_device_group`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_device_group`;
CREATE TABLE `qinfo_device_group` (
  `group_id` int(10) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) DEFAULT '',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- Records of `qinfo_device_group`
-- -----------------------------
INSERT INTO `qinfo_device_group` VALUES ('1', '系统');
INSERT INTO `qinfo_device_group` VALUES ('2', '其它');

-- -----------------------------
-- Table structure for `qinfo_device_monitor`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_device_monitor`;
CREATE TABLE `qinfo_device_monitor` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) DEFAULT '',
  `device_id` int(10) NOT NULL,
  `type` varchar(255) DEFAULT 'snmp',
  `status` tinyint(4) DEFAULT '0',
  `remark` varchar(255) DEFAULT '',
  `snmp_name` varchar(255) DEFAULT '',
  `snmp_config` varchar(255) DEFAULT '',
  `snmp_port` int(10) DEFAULT '161',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- -----------------------------
-- Table structure for `qinfo_district`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_district`;
CREATE TABLE `qinfo_district` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `level` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `upid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=45052 DEFAULT CHARSET=utf8 COMMENT='中国省市区乡镇数据表';

-- -----------------------------
-- Records of `qinfo_district`
-- -----------------------------
INSERT INTO `qinfo_district` VALUES ('1', '北京市', '1', '0');
INSERT INTO `qinfo_district` VALUES ('2', '天津市', '1', '0');
INSERT INTO `qinfo_district` VALUES ('3', '河北省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('4', '山西省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('5', '内蒙古自治区', '1', '0');
INSERT INTO `qinfo_district` VALUES ('6', '辽宁省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('7', '吉林省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('8', '黑龙江省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('9', '上海市', '1', '0');
INSERT INTO `qinfo_district` VALUES ('10', '江苏省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('11', '浙江省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('12', '安徽省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('13', '福建省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('14', '江西省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('15', '山东省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('16', '河南省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('17', '湖北省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('18', '湖南省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('19', '广东省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('20', '广西壮族自治区', '1', '0');
INSERT INTO `qinfo_district` VALUES ('21', '海南省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('22', '重庆市', '1', '0');
INSERT INTO `qinfo_district` VALUES ('23', '四川省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('24', '贵州省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('25', '云南省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('26', '西藏自治区', '1', '0');
INSERT INTO `qinfo_district` VALUES ('27', '陕西省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('28', '甘肃省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('29', '青海省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('30', '宁夏回族自治区', '1', '0');
INSERT INTO `qinfo_district` VALUES ('31', '新疆维吾尔自治区', '1', '0');
INSERT INTO `qinfo_district` VALUES ('32', '台湾省', '1', '0');
INSERT INTO `qinfo_district` VALUES ('33', '香港特别行政区', '1', '0');
INSERT INTO `qinfo_district` VALUES ('34', '澳门特别行政区', '1', '0');
INSERT INTO `qinfo_district` VALUES ('35', '海外', '1', '0');
INSERT INTO `qinfo_district` VALUES ('36', '其他', '1', '0');
INSERT INTO `qinfo_district` VALUES ('37', '东城区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('38', '西城区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('39', '崇文区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('40', '宣武区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('41', '朝阳区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('42', '丰台区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('43', '石景山区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('44', '海淀区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('45', '门头沟区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('46', '房山区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('47', '通州区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('48', '顺义区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('49', '昌平区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('50', '大兴区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('51', '怀柔区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('52', '平谷区', '2', '1');
INSERT INTO `qinfo_district` VALUES ('53', '密云县', '2', '1');
INSERT INTO `qinfo_district` VALUES ('54', '延庆县', '2', '1');
INSERT INTO `qinfo_district` VALUES ('55', '和平区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('56', '河东区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('57', '河西区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('58', '南开区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('59', '河北区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('60', '红桥区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('61', '塘沽区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('62', '汉沽区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('63', '大港区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('64', '东丽区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('65', '西青区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('66', '津南区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('67', '北辰区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('68', '武清区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('69', '宝坻区', '2', '2');
INSERT INTO `qinfo_district` VALUES ('70', '宁河县', '2', '2');
INSERT INTO `qinfo_district` VALUES ('71', '静海县', '2', '2');
INSERT INTO `qinfo_district` VALUES ('72', '蓟县', '2', '2');
INSERT INTO `qinfo_district` VALUES ('73', '石家庄市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('74', '唐山市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('75', '秦皇岛市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('76', '邯郸市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('77', '邢台市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('78', '保定市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('79', '张家口市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('80', '承德市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('81', '衡水市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('82', '廊坊市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('83', '沧州市', '2', '3');
INSERT INTO `qinfo_district` VALUES ('84', '太原市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('85', '大同市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('86', '阳泉市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('87', '长治市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('88', '晋城市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('89', '朔州市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('90', '晋中市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('91', '运城市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('92', '忻州市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('93', '临汾市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('94', '吕梁市', '2', '4');
INSERT INTO `qinfo_district` VALUES ('95', '呼和浩特市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('96', '包头市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('97', '乌海市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('98', '赤峰市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('99', '通辽市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('100', '鄂尔多斯市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('101', '呼伦贝尔市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('102', '巴彦淖尔市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('103', '乌兰察布市', '2', '5');
INSERT INTO `qinfo_district` VALUES ('104', '兴安盟', '2', '5');
INSERT INTO `qinfo_district` VALUES ('105', '锡林郭勒盟', '2', '5');
INSERT INTO `qinfo_district` VALUES ('106', '阿拉善盟', '2', '5');
INSERT INTO `qinfo_district` VALUES ('107', '沈阳市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('108', '大连市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('109', '鞍山市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('110', '抚顺市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('111', '本溪市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('112', '丹东市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('113', '锦州市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('114', '营口市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('115', '阜新市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('116', '辽阳市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('117', '盘锦市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('118', '铁岭市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('119', '朝阳市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('120', '葫芦岛市', '2', '6');
INSERT INTO `qinfo_district` VALUES ('121', '长春市', '2', '7');
INSERT INTO `qinfo_district` VALUES ('122', '吉林市', '2', '7');
INSERT INTO `qinfo_district` VALUES ('123', '四平市', '2', '7');
INSERT INTO `qinfo_district` VALUES ('124', '辽源市', '2', '7');
INSERT INTO `qinfo_district` VALUES ('125', '通化市', '2', '7');
INSERT INTO `qinfo_district` VALUES ('126', '白山市', '2', '7');
INSERT INTO `qinfo_district` VALUES ('127', '松原市', '2', '7');
INSERT INTO `qinfo_district` VALUES ('128', '白城市', '2', '7');
INSERT INTO `qinfo_district` VALUES ('129', '延边朝鲜族自治州', '2', '7');
INSERT INTO `qinfo_district` VALUES ('130', '哈尔滨市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('131', '齐齐哈尔市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('132', '鸡西市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('133', '鹤岗市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('134', '双鸭山市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('135', '大庆市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('136', '伊春市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('137', '佳木斯市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('138', '七台河市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('139', '牡丹江市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('140', '黑河市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('141', '绥化市', '2', '8');
INSERT INTO `qinfo_district` VALUES ('142', '大兴安岭地区', '2', '8');
INSERT INTO `qinfo_district` VALUES ('143', '黄浦区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('144', '卢湾区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('145', '徐汇区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('146', '长宁区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('147', '静安区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('148', '普陀区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('149', '闸北区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('150', '虹口区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('151', '杨浦区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('152', '闵行区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('153', '宝山区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('154', '嘉定区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('155', '浦东新区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('156', '金山区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('157', '松江区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('158', '青浦区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('159', '南汇区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('160', '奉贤区', '2', '9');
INSERT INTO `qinfo_district` VALUES ('161', '崇明县', '2', '9');
INSERT INTO `qinfo_district` VALUES ('162', '南京市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('163', '无锡市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('164', '徐州市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('165', '常州市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('166', '苏州市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('167', '南通市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('168', '连云港市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('169', '淮安市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('170', '盐城市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('171', '扬州市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('172', '镇江市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('173', '泰州市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('174', '宿迁市', '2', '10');
INSERT INTO `qinfo_district` VALUES ('175', '杭州市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('176', '宁波市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('177', '温州市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('178', '嘉兴市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('179', '湖州市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('180', '绍兴市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('181', '舟山市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('182', '衢州市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('183', '金华市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('184', '台州市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('185', '丽水市', '2', '11');
INSERT INTO `qinfo_district` VALUES ('186', '合肥市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('187', '芜湖市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('188', '蚌埠市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('189', '淮南市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('190', '马鞍山市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('191', '淮北市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('192', '铜陵市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('193', '安庆市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('194', '黄山市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('195', '滁州市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('196', '阜阳市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('197', '宿州市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('198', '巢湖市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('199', '六安市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('200', '亳州市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('201', '池州市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('202', '宣城市', '2', '12');
INSERT INTO `qinfo_district` VALUES ('203', '福州市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('204', '厦门市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('205', '莆田市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('206', '三明市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('207', '泉州市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('208', '漳州市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('209', '南平市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('210', '龙岩市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('211', '宁德市', '2', '13');
INSERT INTO `qinfo_district` VALUES ('212', '南昌市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('213', '景德镇市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('214', '萍乡市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('215', '九江市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('216', '新余市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('217', '鹰潭市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('218', '赣州市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('219', '吉安市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('220', '宜春市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('221', '抚州市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('222', '上饶市', '2', '14');
INSERT INTO `qinfo_district` VALUES ('223', '济南市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('224', '青岛市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('225', '淄博市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('226', '枣庄市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('227', '东营市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('228', '烟台市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('229', '潍坊市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('230', '济宁市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('231', '泰安市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('232', '威海市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('233', '日照市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('234', '莱芜市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('235', '临沂市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('236', '德州市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('237', '聊城市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('238', '滨州市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('239', '菏泽市', '2', '15');
INSERT INTO `qinfo_district` VALUES ('240', '郑州市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('241', '开封市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('242', '洛阳市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('243', '平顶山市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('244', '安阳市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('245', '鹤壁市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('246', '新乡市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('247', '焦作市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('248', '濮阳市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('249', '许昌市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('250', '漯河市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('251', '三门峡市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('252', '南阳市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('253', '商丘市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('254', '信阳市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('255', '周口市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('256', '驻马店市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('257', '济源市', '2', '16');
INSERT INTO `qinfo_district` VALUES ('258', '武汉市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('259', '黄石市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('260', '十堰市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('261', '宜昌市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('262', '襄樊市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('263', '鄂州市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('264', '荆门市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('265', '孝感市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('266', '荆州市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('267', '黄冈市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('268', '咸宁市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('269', '随州市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('270', '恩施土家族苗族自治州', '2', '17');
INSERT INTO `qinfo_district` VALUES ('271', '仙桃市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('272', '潜江市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('273', '天门市', '2', '17');
INSERT INTO `qinfo_district` VALUES ('274', '神农架林区', '2', '17');
INSERT INTO `qinfo_district` VALUES ('275', '长沙市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('276', '株洲市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('277', '湘潭市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('278', '衡阳市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('279', '邵阳市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('280', '岳阳市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('281', '常德市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('282', '张家界市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('283', '益阳市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('284', '郴州市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('285', '永州市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('286', '怀化市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('287', '娄底市', '2', '18');
INSERT INTO `qinfo_district` VALUES ('288', '湘西土家族苗族自治州', '2', '18');
INSERT INTO `qinfo_district` VALUES ('289', '广州市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('290', '韶关市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('291', '深圳市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('292', '珠海市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('293', '汕头市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('294', '佛山市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('295', '江门市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('296', '湛江市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('297', '茂名市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('298', '肇庆市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('299', '惠州市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('300', '梅州市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('301', '汕尾市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('302', '河源市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('303', '阳江市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('304', '清远市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('305', '东莞市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('306', '中山市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('307', '潮州市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('308', '揭阳市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('309', '云浮市', '2', '19');
INSERT INTO `qinfo_district` VALUES ('310', '南宁市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('311', '柳州市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('312', '桂林市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('313', '梧州市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('314', '北海市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('315', '防城港市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('316', '钦州市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('317', '贵港市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('318', '玉林市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('319', '百色市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('320', '贺州市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('321', '河池市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('322', '来宾市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('323', '崇左市', '2', '20');
INSERT INTO `qinfo_district` VALUES ('324', '海口市', '2', '21');
INSERT INTO `qinfo_district` VALUES ('325', '三亚市', '2', '21');
INSERT INTO `qinfo_district` VALUES ('326', '五指山市', '2', '21');
INSERT INTO `qinfo_district` VALUES ('327', '琼海市', '2', '21');
INSERT INTO `qinfo_district` VALUES ('328', '儋州市', '2', '21');
INSERT INTO `qinfo_district` VALUES ('329', '文昌市', '2', '21');
INSERT INTO `qinfo_district` VALUES ('330', '万宁市', '2', '21');
INSERT INTO `qinfo_district` VALUES ('331', '东方市', '2', '21');
INSERT INTO `qinfo_district` VALUES ('332', '定安县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('333', '屯昌县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('334', '澄迈县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('335', '临高县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('336', '白沙黎族自治县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('337', '昌江黎族自治县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('338', '乐东黎族自治县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('339', '陵水黎族自治县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('340', '保亭黎族苗族自治县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('341', '琼中黎族苗族自治县', '2', '21');
INSERT INTO `qinfo_district` VALUES ('342', '西沙群岛', '2', '21');
INSERT INTO `qinfo_district` VALUES ('343', '南沙群岛', '2', '21');
INSERT INTO `qinfo_district` VALUES ('344', '中沙群岛的岛礁及其海域', '2', '21');
INSERT INTO `qinfo_district` VALUES ('345', '万州区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('346', '涪陵区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('347', '渝中区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('348', '大渡口区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('349', '江北区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('350', '沙坪坝区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('351', '九龙坡区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('352', '南岸区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('353', '北碚区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('354', '双桥区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('355', '万盛区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('356', '渝北区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('357', '巴南区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('358', '黔江区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('359', '长寿区', '2', '22');
INSERT INTO `qinfo_district` VALUES ('360', '綦江县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('361', '潼南县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('362', '铜梁县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('363', '大足县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('364', '荣昌县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('365', '璧山县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('366', '梁平县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('367', '城口县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('368', '丰都县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('369', '垫江县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('370', '武隆县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('371', '忠县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('372', '开县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('373', '云阳县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('374', '奉节县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('375', '巫山县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('376', '巫溪县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('377', '石柱土家族自治县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('378', '秀山土家族苗族自治县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('379', '酉阳土家族苗族自治县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('380', '彭水苗族土家族自治县', '2', '22');
INSERT INTO `qinfo_district` VALUES ('381', '江津市', '2', '22');
INSERT INTO `qinfo_district` VALUES ('382', '合川市', '2', '22');
INSERT INTO `qinfo_district` VALUES ('383', '永川市', '2', '22');
INSERT INTO `qinfo_district` VALUES ('384', '南川市', '2', '22');
INSERT INTO `qinfo_district` VALUES ('385', '成都市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('386', '自贡市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('387', '攀枝花市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('388', '泸州市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('389', '德阳市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('390', '绵阳市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('391', '广元市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('392', '遂宁市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('393', '内江市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('394', '乐山市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('395', '南充市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('396', '眉山市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('397', '宜宾市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('398', '广安市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('399', '达州市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('400', '雅安市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('401', '巴中市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('402', '资阳市', '2', '23');
INSERT INTO `qinfo_district` VALUES ('403', '阿坝藏族羌族自治州', '2', '23');
INSERT INTO `qinfo_district` VALUES ('404', '甘孜藏族自治州', '2', '23');
INSERT INTO `qinfo_district` VALUES ('405', '凉山彝族自治州', '2', '23');
INSERT INTO `qinfo_district` VALUES ('406', '贵阳市', '2', '24');
INSERT INTO `qinfo_district` VALUES ('407', '六盘水市', '2', '24');
INSERT INTO `qinfo_district` VALUES ('408', '遵义市', '2', '24');
INSERT INTO `qinfo_district` VALUES ('409', '安顺市', '2', '24');
INSERT INTO `qinfo_district` VALUES ('410', '铜仁地区', '2', '24');
INSERT INTO `qinfo_district` VALUES ('411', '黔西南布依族苗族自治州', '2', '24');
INSERT INTO `qinfo_district` VALUES ('412', '毕节地区', '2', '24');
INSERT INTO `qinfo_district` VALUES ('413', '黔东南苗族侗族自治州', '2', '24');
INSERT INTO `qinfo_district` VALUES ('414', '黔南布依族苗族自治州', '2', '24');
INSERT INTO `qinfo_district` VALUES ('415', '昆明市', '2', '25');
INSERT INTO `qinfo_district` VALUES ('416', '曲靖市', '2', '25');
INSERT INTO `qinfo_district` VALUES ('417', '玉溪市', '2', '25');
INSERT INTO `qinfo_district` VALUES ('418', '保山市', '2', '25');
INSERT INTO `qinfo_district` VALUES ('419', '昭通市', '2', '25');
INSERT INTO `qinfo_district` VALUES ('420', '丽江市', '2', '25');
INSERT INTO `qinfo_district` VALUES ('421', '思茅市', '2', '25');
INSERT INTO `qinfo_district` VALUES ('422', '临沧市', '2', '25');
INSERT INTO `qinfo_district` VALUES ('423', '楚雄彝族自治州', '2', '25');
INSERT INTO `qinfo_district` VALUES ('424', '红河哈尼族彝族自治州', '2', '25');
INSERT INTO `qinfo_district` VALUES ('425', '文山壮族苗族自治州', '2', '25');
INSERT INTO `qinfo_district` VALUES ('426', '西双版纳傣族自治州', '2', '25');
INSERT INTO `qinfo_district` VALUES ('427', '大理白族自治州', '2', '25');
INSERT INTO `qinfo_district` VALUES ('428', '德宏傣族景颇族自治州', '2', '25');
INSERT INTO `qinfo_district` VALUES ('429', '怒江傈僳族自治州', '2', '25');
INSERT INTO `qinfo_district` VALUES ('430', '迪庆藏族自治州', '2', '25');
INSERT INTO `qinfo_district` VALUES ('431', '拉萨市', '2', '26');
INSERT INTO `qinfo_district` VALUES ('432', '昌都地区', '2', '26');
INSERT INTO `qinfo_district` VALUES ('433', '山南地区', '2', '26');
INSERT INTO `qinfo_district` VALUES ('434', '日喀则地区', '2', '26');
INSERT INTO `qinfo_district` VALUES ('435', '那曲地区', '2', '26');
INSERT INTO `qinfo_district` VALUES ('436', '阿里地区', '2', '26');
INSERT INTO `qinfo_district` VALUES ('437', '林芝地区', '2', '26');
INSERT INTO `qinfo_district` VALUES ('438', '西安市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('439', '铜川市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('440', '宝鸡市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('441', '咸阳市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('442', '渭南市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('443', '延安市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('444', '汉中市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('445', '榆林市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('446', '安康市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('447', '商洛市', '2', '27');
INSERT INTO `qinfo_district` VALUES ('448', '兰州市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('449', '嘉峪关市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('450', '金昌市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('451', '白银市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('452', '天水市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('453', '武威市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('454', '张掖市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('455', '平凉市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('456', '酒泉市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('457', '庆阳市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('458', '定西市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('459', '陇南市', '2', '28');
INSERT INTO `qinfo_district` VALUES ('460', '临夏回族自治州', '2', '28');
INSERT INTO `qinfo_district` VALUES ('461', '甘南藏族自治州', '2', '28');
INSERT INTO `qinfo_district` VALUES ('462', '西宁市', '2', '29');
INSERT INTO `qinfo_district` VALUES ('463', '海东地区', '2', '29');
INSERT INTO `qinfo_district` VALUES ('464', '海北藏族自治州', '2', '29');
INSERT INTO `qinfo_district` VALUES ('465', '黄南藏族自治州', '2', '29');
INSERT INTO `qinfo_district` VALUES ('466', '海南藏族自治州', '2', '29');
INSERT INTO `qinfo_district` VALUES ('467', '果洛藏族自治州', '2', '29');
INSERT INTO `qinfo_district` VALUES ('468', '玉树藏族自治州', '2', '29');
INSERT INTO `qinfo_district` VALUES ('469', '海西蒙古族藏族自治州', '2', '29');
INSERT INTO `qinfo_district` VALUES ('470', '银川市', '2', '30');
INSERT INTO `qinfo_district` VALUES ('471', '石嘴山市', '2', '30');
INSERT INTO `qinfo_district` VALUES ('472', '吴忠市', '2', '30');
INSERT INTO `qinfo_district` VALUES ('473', '固原市', '2', '30');
INSERT INTO `qinfo_district` VALUES ('474', '中卫市', '2', '30');
INSERT INTO `qinfo_district` VALUES ('475', '乌鲁木齐市', '2', '31');
INSERT INTO `qinfo_district` VALUES ('476', '克拉玛依市', '2', '31');
INSERT INTO `qinfo_district` VALUES ('477', '吐鲁番地区', '2', '31');
INSERT INTO `qinfo_district` VALUES ('478', '哈密地区', '2', '31');
INSERT INTO `qinfo_district` VALUES ('479', '昌吉回族自治州', '2', '31');
INSERT INTO `qinfo_district` VALUES ('480', '博尔塔拉蒙古自治州', '2', '31');
INSERT INTO `qinfo_district` VALUES ('481', '巴音郭楞蒙古自治州', '2', '31');
INSERT INTO `qinfo_district` VALUES ('482', '阿克苏地区', '2', '31');
INSERT INTO `qinfo_district` VALUES ('483', '克孜勒苏柯尔克孜自治州', '2', '31');
INSERT INTO `qinfo_district` VALUES ('484', '喀什地区', '2', '31');
INSERT INTO `qinfo_district` VALUES ('485', '和田地区', '2', '31');
INSERT INTO `qinfo_district` VALUES ('486', '伊犁哈萨克自治州', '2', '31');
INSERT INTO `qinfo_district` VALUES ('487', '塔城地区', '2', '31');
INSERT INTO `qinfo_district` VALUES ('488', '阿勒泰地区', '2', '31');
INSERT INTO `qinfo_district` VALUES ('489', '石河子市', '2', '31');
INSERT INTO `qinfo_district` VALUES ('490', '阿拉尔市', '2', '31');
INSERT INTO `qinfo_district` VALUES ('491', '图木舒克市', '2', '31');
INSERT INTO `qinfo_district` VALUES ('492', '五家渠市', '2', '31');
INSERT INTO `qinfo_district` VALUES ('493', '台北市', '2', '32');
INSERT INTO `qinfo_district` VALUES ('494', '高雄市', '2', '32');
INSERT INTO `qinfo_district` VALUES ('495', '基隆市', '2', '32');
INSERT INTO `qinfo_district` VALUES ('496', '台中市', '2', '32');
INSERT INTO `qinfo_district` VALUES ('497', '台南市', '2', '32');
INSERT INTO `qinfo_district` VALUES ('498', '新竹市', '2', '32');
INSERT INTO `qinfo_district` VALUES ('499', '嘉义市', '2', '32');
INSERT INTO `qinfo_district` VALUES ('500', '台北县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('501', '宜兰县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('502', '桃园县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('503', '新竹县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('504', '苗栗县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('505', '台中县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('506', '彰化县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('507', '南投县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('508', '云林县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('509', '嘉义县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('510', '台南县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('511', '高雄县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('512', '屏东县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('513', '澎湖县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('514', '台东县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('515', '花莲县', '2', '32');
INSERT INTO `qinfo_district` VALUES ('516', '中西区', '2', '33');
INSERT INTO `qinfo_district` VALUES ('517', '东区', '2', '33');
INSERT INTO `qinfo_district` VALUES ('518', '九龙城区', '2', '33');
INSERT INTO `qinfo_district` VALUES ('519', '观塘区', '2', '33');
INSERT INTO `qinfo_district` VALUES ('520', '南区', '2', '33');
INSERT INTO `qinfo_district` VALUES ('521', '深水埗区', '2', '33');
INSERT INTO `qinfo_district` VALUES ('522', '黄大仙区', '2', '33');
INSERT INTO `qinfo_district` VALUES ('523', '湾仔区', '2', '33');

-- -----------------------------
-- Table structure for `qinfo_file`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_file`;
CREATE TABLE `qinfo_file` (
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
-- Table structure for `qinfo_hooks`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_hooks`;
CREATE TABLE `qinfo_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addons` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子挂载的插件 ''，''分割',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_hooks`
-- -----------------------------
INSERT INTO `qinfo_hooks` VALUES ('1', 'pageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', '1', '0', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('2', 'pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', '1', '0', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('3', 'documentEditForm', '添加编辑表单的 扩展内容钩子', '1', '0', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('4', 'documentDetailAfter', '文档末尾显示', '1', '0', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('5', 'documentDetailBefore', '页面内容前显示用钩子', '1', '0', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('6', 'documentSaveComplete', '保存文档数据后的扩展钩子', '2', '0', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('7', 'documentEditFormContent', '添加编辑表单的内容显示钩子', '1', '0', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('8', 'adminArticleEdit', '后台内容编辑页编辑器', '1', '1378982734', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('13', 'AdminIndex', '首页小格子个性化显示', '1', '1382596073', 'Sitestat,Monitor,Saleinfo,Devteam,Systeminfo', '1');
INSERT INTO `qinfo_hooks` VALUES ('14', 'topicComment', '评论提交方式扩展钩子。', '1', '1380163518', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('16', 'app_begin', '应用开始', '2', '1384481614', '', '1');
INSERT INTO `qinfo_hooks` VALUES ('17', 'J_China_City', '每个系统都需要的一个中国省市区三级联动插件。', '1', '1455877345', '', '1');

-- ----------------------------
-- Table structure for `qinfo_log`
-- ----------------------------
DROP TABLE IF EXISTS `qinfo_log`;
CREATE TABLE `qinfo_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '模块',
  `record_id` varchar(255) NOT NULL DEFAULT '0' COMMENT '触发模块的数据id',
  `level` int(10) NOT NULL DEFAULT '0' COMMENT '0常规1:警告2:严重',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行的时间',
  `extended_data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='日志报表';

-- -----------------------------
-- Table structure for `qinfo_member`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_member`;
CREATE TABLE `qinfo_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '用户密码',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱地址',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` varchar(20) NOT NULL DEFAULT '' COMMENT '生日',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `signature` text COMMENT '用户签名',
  `pos_province` int(11) DEFAULT '0' COMMENT '用户所在省份',
  `pos_city` int(11) DEFAULT '0' COMMENT '用户所在城市',
  `pos_district` int(11) DEFAULT '0' COMMENT '用户所在县城',
  `pos_community` int(11) DEFAULT '0' COMMENT '用户所在区域',
  `salt` varchar(255) NOT NULL DEFAULT '' COMMENT '密码盐值',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  PRIMARY KEY (`uid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='会员表';

-- -----------------------------
-- Records of `qinfo_member`
-- -----------------------------
INSERT INTO `qinfo_member` VALUES ('1', 'administrator', '624fb30cc506304e3f2150c169a66d3b', '齐信管理员', 'administrator@qinfo360.com', '', '0', '', '', '0', '', '0', '0', '0', '0', 'utIgsO', '0', '0', '1497845343', '2130706433', '1498533282', '1');
INSERT INTO `qinfo_member` VALUES ('2', 'admin', 'ad98996fa5d69301400a820a9f9f3d0c', '系统管理员', 'admin@163.com', '', '0', '', '', '0', '', '0', '0', '0', '0', 'VsJzvG', '0', '0', '1497845343', '2130706433', '1498532941', '1');
INSERT INTO `qinfo_member` VALUES ('3', 'securityadmin', 'ad98996fa5d69301400a820a9f9f3d0c', '安全管理员', 'security@163.com', '', '0', '', '', '0', '', '0', '0', '0', '0', 'VsJzvG', '0', '0', '1497845343', '2130706433', '1498532941', '1');
INSERT INTO `qinfo_member` VALUES ('4', 'useradmin', 'ad98996fa5d69301400a820a9f9f3d0c', '用户管理员', 'user@163.com', '', '0', '', '', '0', '', '0', '0', '0', '0', 'VsJzvG', '0', '0', '1497845343', '2130706433', '1498532941', '1');
INSERT INTO `qinfo_member` VALUES ('5', 'auditoradmin', 'ad98996fa5d69301400a820a9f9f3d0c', '审计管理员', 'auditor@163.com', '', '0', '', '', '0', '', '0', '0', '0', '0', 'VsJzvG', '0', '0', '1497845343', '2130706433', '1498532941', '1');

-- -----------------------------
-- Table structure for `qinfo_member_extend`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_member_extend`;
CREATE TABLE `qinfo_member_extend` (
  `uid` int(10) NOT NULL COMMENT '用户UID',
  `education` int(10) DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_member_extend`
-- -----------------------------
INSERT INTO `qinfo_member_extend` VALUES ('1', '0');

-- -----------------------------
-- Table structure for `qinfo_member_extend_group`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_member_extend_group`;
CREATE TABLE `qinfo_member_extend_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `name` varchar(50) NOT NULL COMMENT '分组数据表',
  `profile_name` varchar(25) NOT NULL COMMENT '扩展分组名称',
  `createTime` int(10) NOT NULL COMMENT '创建时间',
  `sort` int(10) NOT NULL COMMENT '排序',
  `visiable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可见，1可见，0不可见',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '字段状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_member_extend_group`
-- -----------------------------
INSERT INTO `qinfo_member_extend_group` VALUES ('1', 'member_extend', '个人资料', '1403847366', '0', '1', '1');

-- -----------------------------
-- Table structure for `qinfo_member_extend_setting`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_member_extend_setting`;
CREATE TABLE `qinfo_member_extend_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段注释',
  `length` varchar(100) NOT NULL DEFAULT '' COMMENT '字段定义',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '数据类型',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '参数',
  `is_must` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必填',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员字段表';

-- -----------------------------
-- Records of `qinfo_member_extend_setting`
-- -----------------------------
INSERT INTO `qinfo_member_extend_setting` VALUES ('1', 'education', '学历', '10', 'select', '', '', '1', '1:小学\r\n2:初中\r\n3:高中', '0', '1', '1455930923', '1455930787');

-- -----------------------------
-- Table structure for `qinfo_menu`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_menu`;
CREATE TABLE `qinfo_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `icon` varchar(20) NOT NULL DEFAULT '' COMMENT '分类图标',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `module` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=400 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_menu`
-- -----------------------------
INSERT INTO `qinfo_menu` VALUES ('1', 'home', 'home', '0', '1', 'admin/index/index', '0', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('2', 'system', 'laptop', '0', '99', 'admin/config/group', '0', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('4', 'user', 'user', '0', '98', 'admin/user/index', '0', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('5', 'update_cache', 'refresh', '1', '1', 'admin/index/clear', '0', '', 'home', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('6', 'license', '', '1', '0', 'admin/license/index', '1', '', 'home', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('7', 'auth_activation', '', '1', '0', 'admin/license/license', '1', '', 'home', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('8', 'basic_config', 'cog', '2', '1', 'admin/config/group', '0', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('9', 'menu_manage', 'book', '2', '2', 'admin/menu/index', '0', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('10', 'service_install', '', '2', '3', 'admin/server/index', '0', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('11', 'import_menu', '', '2', '0', 'admin/menu/import', '1', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('13', 'seo_settings', 'anchor', '2', '10', 'admin/seo/index', '0', '', 'optimal_settings', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('16', 'user_list', 'user', '4', '0', 'admin/user/index', '0', '', 'user_manage', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('17', 'user_group', 'users', '4', '0', 'admin/group/index', '0', '', 'user_manage', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('18', 'auth_list', 'paw', '4', '0', 'admin/group/access', '0', '', 'user_manage', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('19', 'user_behavior', 'file-text', '4', '0', 'admin/action/index', '0', '', 'behavior_manage', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('20', 'behavior_log', 'clipboard', '4', '0', 'admin/action/log', '0', '', 'behavior_manage', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('21', 'action_detail', '', '4', '0', 'admin/action/detail', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('22', 'file_upload', '', '1', '0', 'admin/upload/upload', '1', '', 'home', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('23', 'plugin_list', 'usb', '2', '8', 'admin/addons/index', '1', '', 'local', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('24', 'hook_list', 'code', '2', '9', 'admin/addons/hooks', '0', '', 'local', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('26', 'routing_rules', 'slack', '2', '11', 'admin/seo/rewrite', '0', '', 'optimal_settings', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('28', 'console', 'dashboard', '1', '0', 'admin/index/index', '0', '', 'home', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('51', 'edit_user_group', '', '4', '1', 'admin/group/edit', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('30', 'advanced_config', 'cog', '2', '0', 'admin/config/index', '1', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('31', 'add_config', '', '2', '0', 'admin/config/add', '1', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('32', 'edit_config', '', '2', '0', 'admin/config/edit', '1', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('33', 'add_menu', '', '2', '0', 'admin/menu/add', '1', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('34', 'edit_menu', '', '2', '0', 'admin/menu/edit', '1', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('40', 'module_list', 'tag', '2', '7', 'admin/module/index', '0', '', 'local', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('41', 'data_recovery', 'table', '2', '13', 'admin/database/dataimport', '0', '', 'database', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('42', 'data_backup', 'exchange', '2', '12', 'admin/database/index', '0', '', 'database', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('43', 'software_download', '', '2', '4', 'admin/software/index', '0', '', 'system_config', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('44', 'cloud_market', '', '2', '5', 'admin/cloud/index', '1', '', 'cloud_market', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('45', 'auto_update', '', '2', '6', 'admin/cloud/update', '1', '', 'cloud_market', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('46', 'add_user', '', '4', '0', 'admin/user/add', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('47', 'edit_user', '', '4', '0', 'admin/user/edit', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('48', 'del_user', '', '4', '0', 'admin/user/del', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('49', 'auth_user', '', '4', '0', 'admin/user/auth', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('50', 'add_user_group', '', '4', '1', 'admin/group/add', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('52', 'auth_user_group', '', '4', '1', 'admin/group/auth', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('53', 'del_user_group', '', '4', '1', 'admin/group/del', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('54', 'add_user_node', '', '4', '1', 'admin/group/addnode', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('55', 'edit_user_node', '', '4', '1', 'admin/group/editnode', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('56', 'del_user_node', '', '4', '1', 'admin/group/delnode', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('57', 'up_user_node', '', '4', '1', 'admin/group/upnode', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('58', 'add_action', '', '4', '1', 'admin/action/add', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('59', 'edit_action', '', '4', '1', 'admin/action/edit', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('60', 'del_action', '', '4', '1', 'admin/action/del', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('61', 'status_action', '', '4', '1', 'admin/action/setstatus', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('62', 'del_action_log', '', '4', '1', 'admin/action/dellog', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('63', 'del_action_clear', '', '4', '1', 'admin/action/clear', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('64', 'edit_user_pass', '', '4', '1', 'admin/user/editpwd', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('65', 'edit_user_avatar', '', '4', '1', 'admin/user/avatar', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('66', 'add_server', '', '2', '1', 'admin/server/add', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('67', 'edit_server', '', '2', '1', 'admin/server/edit', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('68', 'del_server', '', '2', '1', 'admin/server/del', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('69', 'ssh2_server', '', '2', '1', 'admin/server/ssh2', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('70', 'sendcmd_server', '', '2', '1', 'admin/server/sendcmd', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('71', 'fastcmd_server', '', '2', '1', 'admin/server/fastcmd', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('72', 'command', '', '2', '1', 'admin/server/command', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('73', 'add_command', '', '2', '1', 'admin/server/command_add', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('74', 'edit_command', '', '2', '1', 'admin/server/command_edit', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('75', 'del_command', '', '2', '1', 'admin/server/command_del', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('76', 'link_server', '', '2', '1', 'admin/server/link', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('77', 'download_state', '', '2', '1', 'admin/software/download_state', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('78', 'download_file', '', '2', '1', 'admin/software/download_file', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('79', 'cloud_look_version', '', '2', '1', 'admin/cloud/version', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('80', 'cloud_install', '', '2', '1', 'admin/cloud/install', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('81', 'edit_module', '', '2', '1', 'admin/module/edit', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('82', 'uninstall_module', '', '2', '1', 'admin/module/uninstall', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('83', 'install_module', '', '2', '1', 'admin/module/install', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('84', 'config_addons', '', '2', '1', 'admin/addons/config', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('85', 'uninstall_addons', '', '2', '1', 'admin/addons/uninstall', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('86', 'disable_addons', '', '2', '1', 'admin/addons/disable', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('87', 'enable_addons', '', '2', '1', 'admin/addons/enable', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('88', 'install_addons', '', '2', '1', 'admin/addons/install', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('89', 'export_database', '', '2', '1', 'admin/database/export', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('90', 'optimize_database', '', '2', '1', 'admin/database/optimize', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('91', 'repair_database', '', '2', '1', 'admin/database/repair', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('92', 'del_database', '', '2', '1', 'admin/database/del', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('93', 'import_database', '', '2', '1', 'admin/database/import', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('94', 'cloud_getfilelist', '', '2', '1', 'admin/cloud/getfilelist', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('95', 'cloud_compare', '', '2', '1', 'admin/cloud/compare', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('96', 'cloud_cover', '', '2', '1', 'admin/cloud/cover', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('97', 'cloud_updb', '', '2', '1', 'admin/cloud/updb', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('98', 'cloud_finish', '', '2', '1', 'admin/cloud/finish', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('99', 'del_module', '', '2', '1', 'admin/module/del', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('100', 'manual_upgrade', '', '2', '1', 'admin/module/manual_upgrade', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('101', 'updata_avatar', '', '4', '1', 'admin/user/updata_avatar', '1', '', '', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('102', 'log_report', '', '1', '1', 'admin/log/index', '0', '', 'home', '0', '0', 'admin');
INSERT INTO `qinfo_menu` VALUES ('200', 'device', 'th', '0', '2', 'device/manage/device', '0', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('201', 'device_manage', 'th', '200', '0', 'device/manage/device', '0', '', 'service_device', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('202', 'storage_pool', 'save', '200', '2', 'device/manage/storage', '0', '', 'service_device', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('203', 'compute_pool', 'outdent', '200', '3', 'device/manage/computenode', '0', '', 'service_device', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('204', 'device_group', '', '200', '1', 'device/manage/device_group', '0', '', 'service_device', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('301', 'addcdp_device', '', '200', '1', 'cdp/index/add_device', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('210', 'get_device_lists', '', '200', '1', 'device/manage/get_device_lists', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('211', 'add_device', '', '200', '1', 'device/manage/device_add', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('212', 'get_device', '', '200', '1', 'device/manage/get_device', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('213', 'edit_device', '', '200', '1', 'device/manage/device_edit', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('214', 'del_device', '', '200', '1', 'device/manage/device_del', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('215', 'detail_device', '', '200', '1', 'device/manage/device_detail', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('216', 'get_device_monitor', '', '200', '1', 'device/manage/get_device_monitor', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('217', 'soft_status', '', '200', '1', 'device/manage/soft_status', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('218', 'soft_update', '', '200', '1', 'device/manage/update', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('219', 'soft_uninstall', '', '200', '1', 'device/manage/uninstall', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('220', 'soft_restart', '', '200', '1', 'device/manage/soft_restart', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('221', 'add_device_group', '', '200', '1', 'device/manage/group_add', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('222', 'edit_device_group', '', '200', '1', 'device/manage/group_edit', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('223', 'del_device_group', '', '200', '1', 'device/manage/group_del', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('224', 'edit_device_snmp', '', '200', '1', 'device/manage/snmp_edit', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('225', 'add_storage', '', '200', '1', 'device/manage/storage_add', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('226', 'storage_manage', '', '200', '1', 'device/manage/storage_manage', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('227', 'edit_storage', '', '200', '1', 'device/manage/storage_edit', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('228', 'storage_status', '', '200', '1', 'device/manage/storage_status', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('229', 'del_storage', '', '200', '1', 'device/manage/storage_del', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('230', 'add_storage_space', '', '200', '1', 'device/manage/storage_space_add', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('231', 'status_storage_space', '', '200', '1', 'device/manage/storage_space_status', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('232', 'del_storage_space', '', '200', '1', 'device/manage/storage_space_del', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('233', 'add_compute', '', '200', '1', 'device/manage/compute_add', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('234', 'edit_compute', '', '200', '1', 'device/manage/compute_edit', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('235', 'detail_compute', '', '200', '1', 'device/manage/compute_detail', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('236', 'status_compute', '', '200', '1', 'device/manage/compute_status', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('237', 'vnc_compute', '', '200', '1', 'device/manage/vnc', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('238', 'virtual_detail', '', '200', '1', 'device/manage/virtual_detail', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('239', 'del_virtual', '', '200', '1', 'device/manage/virtual_del', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('240', 'edit_type_virtual', '', '200', '1', 'device/manage/virtual_edit_type', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('241', 'edit_virtual', '', '200', '1', 'device/manage/virtual_edit', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('242', 'add_disk_virtual', '', '200', '1', 'device/manage/add_disk', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('243', 'status_virtual', '', '200', '1', 'device/manage/virtual_status', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('244', 'add_network_virtual', '', '200', '1', 'device/manage/add_network', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('245', 'del_compute', '', '200', '1', 'device/manage/compute_del', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('246', 'update_compute', '', '200', '1', 'device/manage/update_compute', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('247', 'virtual_list', '', '200', '1', 'device/manage/virtual', '1', '', '', '0', '0', 'device');
INSERT INTO `qinfo_menu` VALUES ('300', 'cdp_manage', 'database', '200', '1', 'cdp/index/index', '0', '', 'cloud_backup', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('302', 'delcdp_device', '', '200', '1', 'cdp/index/del_device', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('303', 'detail_cdp', '', '200', '1', 'cdp/index/detail', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('304', 'addcdp_task', '', '200', '1', 'cdp/index/task_add', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('305', 'getcdp_task_snap', '', '200', '1', 'cdp/index/get_task_snap', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('306', 'getcdp_clone_state', '', '200', '1', 'cdp/index/get_clone_state', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('307', 'cdp_task_status', '', '200', '1', 'cdp/index/task_status', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('308', 'cdp_create_snap', '', '200', '1', 'cdp/index/create_snap', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('309', 'cdp_merge_snap', '', '200', '1', 'cdp/index/merge_snap', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('310', 'cdp_del_task', '', '200', '1', 'cdp/index/del_task', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('311', 'cdp_snap_detail', '', '200', '1', 'cdp/index/snap_detail', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('312', 'cdp_auto_conf', '', '200', '1', 'cdp/index/auto_conf', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('313', 'edit_auto_config', '', '200', '1', 'cdp/index/edit_auto_config', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('314', 'add_auto_config', '', '200', '1', 'cdp/index/add_auto_config', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('315', 'add_cdp_virtual', '', '200', '1', 'cdp/index/add_virtual', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('316', 'generate_mac_addr', '', '200', '1', 'device/manage/generate_mac_addr', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('317', 'add_cdp_keli', '', '200', '1', 'cdp/index/add_keli', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('318', 'get_cdp_task', '', '200', '1', 'cdp/index/get_task', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('319', 'edit_cdp_task', '', '200', '1', 'cdp/index/edit_task', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('320', 'del_cdp_snap', '', '200', '1', 'cdp/index/del_cdp_snap', '1', '', '', '0', '0', 'cdp');
INSERT INTO `qinfo_menu` VALUES ('321', 'get_log', '', '200', '1', 'cdp/index/get_log', '1', '', '', '0', '0', 'cdp');


-- -----------------------------
-- Table structure for `qinfo_module`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_module`;
CREATE TABLE `qinfo_module` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '模块名',
  `alias` varchar(30) NOT NULL DEFAULT '' COMMENT '中文名',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '版本号',
  `is_com` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否商业版',
  `show_nav` tinyint(4) NOT NULL COMMENT '是否显示在导航栏中',
  `summary` varchar(200) NOT NULL DEFAULT '' COMMENT '简介',
  `developer` varchar(50) NOT NULL DEFAULT '' COMMENT '开发者',
  `website` varchar(200) NOT NULL DEFAULT '' COMMENT '网址',
  `entry` varchar(50) NOT NULL DEFAULT '' COMMENT '前台入口',
  `is_setup` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否已安装',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '模块排序',
  `icon` varchar(20) NOT NULL DEFAULT '',
  `can_uninstall` tinyint(4) NOT NULL,
  `admin_entry` varchar(50) NOT NULL DEFAULT '',
  `menu_hide` tinyint(4) NOT NULL DEFAULT '0' COMMENT '后台入口隐藏',
  `app_has` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='模块管理表';

-- -----------------------------
-- Records of `qinfo_module`
-- -----------------------------
INSERT INTO `qinfo_module` VALUES ('1', 'device', 'Device', '1.0.0', '0', '1', 'Client、Storage pool、Computing pool', 'Qinfo software technology (Shanghai) Co., Ltd.', 'http://www.qinfo360.com', '', '1', '0', 'th', '0', 'device/index/index', '0', '0');
INSERT INTO `qinfo_module` VALUES ('2', 'cdp', 'Cdp', '1.0.0', '1', '1', 'continual data protection manage module', 'Qinfo software technology (Shanghai) Co., Ltd.', 'http://www.qinfo360.com', '', '1', '0', 'database', '0', 'cdp/index/index', '0', '0');

-- -----------------------------
-- Table structure for `qinfo_picture`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_picture`;
CREATE TABLE `qinfo_picture` (
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
-- Table structure for `qinfo_rewrite`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_rewrite`;
CREATE TABLE `qinfo_rewrite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `rule` varchar(255) NOT NULL DEFAULT '' COMMENT '规则',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT 'url',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='伪静态表';


-- -----------------------------
-- Table structure for `qinfo_seo_rule`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_seo_rule`;
CREATE TABLE `qinfo_seo_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '唯一标识',
  `title` text NOT NULL COMMENT '规则标题',
  `app` varchar(40) DEFAULT " ",
  `controller` varchar(40) DEFAULT " ",
  `action` varchar(40) DEFAULT " ",
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `seo_title` text NOT NULL COMMENT 'SEO标题',
  `seo_keywords` text NOT NULL COMMENT 'SEO关键词',
  `seo_description` text NOT NULL COMMENT 'SEO描述',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_seo_rule`
-- -----------------------------
INSERT INTO `qinfo_seo_rule` VALUES ('1', '整站标题', '*', '*', '*', '1', '灾备管理平台', '灾备管理平台', '灾备管理平台', '7');

-- -----------------------------
-- Table structure for `qinfo_server`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_server`;
CREATE TABLE `qinfo_server` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `host` varchar(50) NOT NULL DEFAULT '' COMMENT '主机地址',
  `port` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '端口',
  `uname` varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(60) NOT NULL DEFAULT 'text' COMMENT '配置类型',
  `remark` varchar(100) DEFAULT '' COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------
-- Table structure for `qinfo_server_command`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_server_command`;
CREATE TABLE `qinfo_server_command` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(50) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `types` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- -----------------------------
-- Table structure for `qinfo_software`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_software`;
CREATE TABLE `qinfo_software` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品ID',
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` char(80) NOT NULL DEFAULT '' COMMENT '标题',
  `url` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `size` int(10) unsigned NOT NULL DEFAULT '1',
  `download_size` int(10) unsigned DEFAULT '0',
  `downloaded` int(10) DEFAULT '0',
  `upload_size` int(4) NOT NULL DEFAULT '0',
  `uploaded` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `qinfo_software`
-- -----------------------------
INSERT INTO `qinfo_software` VALUES ('1', '服务组件', 'ISO', 'http://os.qinfo360.cn/data/service_soft/service.zip', 'data/software/service.zip', '4015378', '0', '0', '0', '0', '1487127716', '0');
INSERT INTO `qinfo_software` VALUES ('2', 'WINDOWS客户端', 'Zip', 'http://os.qinfo360.cn/data/service_soft/client_windows.zip', 'data/software/client_windows.zip', '1690472084', '0', '0', '0', '0', '1487146968', '0');
INSERT INTO `qinfo_software` VALUES ('3', 'LINUX客户端', 'Zip', 'http://os.qinfo360.cn/data/service_soft/client_linux.zip', 'data/software/client_linux.zip', '1690472084', '0', '0', '0', '0', '1487146968', '0');

-- -----------------------------
-- Table structure for `qinfo_storage`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_storage`;
CREATE TABLE `qinfo_storage` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) DEFAULT '',
  `name` varchar(128) DEFAULT '',
  `type` tinyint(3) DEFAULT '1',
  `size` bigint(20) DEFAULT '0',
  `used_size` bigint(20) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  `remark` varchar(255) DEFAULT '',
  `creation_time` int(10) DEFAULT '0',
  `update_time` int(10) DEFAULT '0',
  `config` text,
  `data` text,
  `is_primary` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- Records of `qinfo_storage`
-- -----------------------------
INSERT INTO `qinfo_storage` VALUES ('1', '', 'local', '1', '10737418240', '0', '1', '', '1500518986', '0', '{\"ip\":\"127.0.0.1\",\"port\":\"5001\",\"secretkeys\":\"\"}', '', '1');

-- -----------------------------
-- Table structure for `qinfo_storage_path`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_storage_path`;
CREATE TABLE `qinfo_storage_path` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `storage_id` int(10) DEFAULT '0',
  `name` varchar(20) NOT NULL DEFAULT '',
  `path` text,
  `type` int(10) DEFAULT '0',
  `used_size` bigint(20) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  `remark` text,
  `is_primary` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- -----------------------------
-- Records of `qinfo_storage_path`
-- -----------------------------
INSERT INTO `qinfo_storage_path` VALUES ('1', '1', 'default', '/default', '1', '0', '1', '', '1');

-- -----------------------------
-- Table structure for `qinfo_sync_login`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_sync_login`;
CREATE TABLE `qinfo_sync_login` (
  `uid` int(10) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `refresh_token` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- -----------------------------
-- Table structure for `qinfo_version`
-- -----------------------------
DROP TABLE IF EXISTS `qinfo_version`;
CREATE TABLE `qinfo_version` (
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '版本号',
  `number` int(10) NOT NULL COMMENT '序列号，一般用日期数字标示',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '版本名',
  `create_time` int(10) NOT NULL COMMENT '发布时间',
  `update_time` int(10) NOT NULL COMMENT '更新的时间',
  `log` text NOT NULL COMMENT '更新日志',
  `url` varchar(150) DEFAULT '' COMMENT '链接到的远程文章',
  `is_current` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`),
  KEY `id` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='自动更新表';

-- -----------------------------
-- Records of `qinfo_version`
-- -----------------------------
INSERT INTO `qinfo_version` VALUES ('3.0.0', '20161213', 'qinfo全新数据管理平台', '1481526669', '1434503121', '1.全新后台UI\r\n2.云端模块管理\r\n3.新增设备管理\r\n4.重构存储池\r\n5.重构计算池', 'http://www.qinfo360.com/', '0');
