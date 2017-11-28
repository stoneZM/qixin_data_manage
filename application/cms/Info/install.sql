
-- -----------------------------
-- 表结构 `qinfo_ad`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `place_id` int(11) DEFAULT NULL COMMENT '广告位ID',
  `title` varchar(150) DEFAULT NULL COMMENT '广告名称',
  `cover_id` int(11) DEFAULT NULL COMMENT '广告图片',
  `photolist` varchar(20) NOT NULL COMMENT '辅助图片',
  `url` varchar(150) DEFAULT NULL COMMENT '广告链接',
  `listurl` varchar(255) DEFAULT NULL COMMENT '辅助链接',
  `background` varchar(150) DEFAULT NULL COMMENT '广告背景',
  `content` text COMMENT '广告描述',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '广告位状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='广告表';


-- -----------------------------
-- 表结构 `qinfo_ad_place`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_ad_place` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(150) DEFAULT NULL COMMENT '广告位名称',
  `name` varchar(20) NOT NULL COMMENT '调用名称',
  `show_type` int(11) NOT NULL DEFAULT '5' COMMENT '广告位类型',
  `show_num` int(11) NOT NULL DEFAULT '5' COMMENT '显示条数',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `template` varchar(150) DEFAULT NULL COMMENT '广告位模板',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '广告位状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='广告位表';


-- -----------------------------
-- 表结构 `qinfo_attribute`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段注释',
  `length` varchar(100) NOT NULL DEFAULT '' COMMENT '字段定义',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '数据类型',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '参数',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `is_must` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必填',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `validate_rule` varchar(255) NOT NULL DEFAULT '',
  `validate_time` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `error_info` varchar(100) NOT NULL DEFAULT '',
  `validate_type` varchar(25) NOT NULL DEFAULT '',
  `auto_rule` varchar(100) NOT NULL DEFAULT '',
  `auto_time` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `auto_type` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COMMENT='模型属性表';


-- -----------------------------
-- 表结构 `qinfo_category`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `name` varchar(30) NOT NULL COMMENT '标志',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `list_row` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '列表每页行数',
  `meta_title` varchar(50) NOT NULL DEFAULT '' COMMENT 'SEO的网页标题',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `template_index` varchar(100) NOT NULL DEFAULT '' COMMENT '频道页模板',
  `template_lists` varchar(100) NOT NULL DEFAULT '' COMMENT '列表页模板',
  `template_detail` varchar(100) NOT NULL DEFAULT '' COMMENT '详情页模板',
  `template_edit` varchar(100) NOT NULL DEFAULT '' COMMENT '编辑页模板',
  `model` varchar(100) NOT NULL DEFAULT '' COMMENT '列表绑定模型',
  `model_sub` varchar(100) NOT NULL DEFAULT '' COMMENT '子文档绑定模型',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '允许发布的内容类型',
  `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '外链',
  `allow_publish` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许发布内容',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '可见性',
  `reply` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许回复',
  `check` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发布的文章是否需要审核',
  `reply_model` varchar(100) NOT NULL DEFAULT '',
  `extend` text COMMENT '扩展设置',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  `icon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类图标',
  `groups` varchar(255) NOT NULL DEFAULT '' COMMENT '分组定义',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='分类表';


-- -----------------------------
-- 表结构 `qinfo_channel`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '频道ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级频道ID',
  `title` char(30) NOT NULL COMMENT '频道标题',
  `url` char(100) NOT NULL COMMENT '频道连接',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
  `icon` varchar(20) DEFAULT NULL COMMENT '图标',
  `color` varchar(20) DEFAULT NULL COMMENT '导航颜色',
  `band_color` varchar(20) DEFAULT NULL COMMENT '标识点颜色',
  `band_text` varchar(30) DEFAULT NULL COMMENT '标志点文字',
  `active` char(100) NOT NULL DEFAULT '' COMMENT '当前链接',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `target` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '新窗口打开',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_document`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `name` char(40) NOT NULL DEFAULT '' COMMENT '标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '标题',
  `category_id` int(10) unsigned NOT NULL COMMENT '所属分类',
  `description` char(140) NOT NULL DEFAULT '' COMMENT '描述',
  `model_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  `position` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '推荐位',
  `link_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '外链',
  `cover_id` int(10) unsigned DEFAULT NULL COMMENT '封面',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可见性',
  `deadline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截至时间',
  `attach` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件数量',
  `view` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `comment` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `extend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '扩展统计字段',
  `level` int(10) NOT NULL DEFAULT '0' COMMENT '优先级',
  `is_top` int(2) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  PRIMARY KEY (`id`),
  KEY `idx_category_status` (`category_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型基础表';


-- -----------------------------
-- 表结构 `qinfo_document_article`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_document_article` (
  `doc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `content` text,
  `tags` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`doc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='文章';


-- -----------------------------
-- 表结构 `qinfo_document_photo`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_document_photo` (
  `doc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `photo_list` varchar(50) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`doc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='图片';


-- -----------------------------
-- 表结构 `qinfo_link`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_link` (
  `id` int(5) NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `ftype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:友情链接 1:合作单位',
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '标题',
  `url` varchar(150) NOT NULL DEFAULT '' COMMENT '链接地址',
  `cover_id` int(11) NOT NULL DEFAULT '0' COMMENT '封面图片ID',
  `descrip` varchar(255) NOT NULL DEFAULT '' COMMENT '备注信息',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `hits` tinyint(7) NOT NULL DEFAULT '0' COMMENT '点击率',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `uid` int(7) NOT NULL DEFAULT '0' COMMENT '用户ID ',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `qinfo_model`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '模型标识',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `extend` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '继承的模型',
  `icon` varchar(20) NOT NULL COMMENT '模型图标',
  `relation` varchar(30) NOT NULL DEFAULT '' COMMENT '继承与被继承模型的关联字段',
  `is_user_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否会员中心显示',
  `need_pk` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '新建表时是否需要主键字段',
  `field_sort` text COMMENT '表单字段排序',
  `field_group` varchar(255) NOT NULL DEFAULT '1:基础' COMMENT '字段分组',
  `field_list` text COMMENT '字段列表',
  `attribute_list` text COMMENT '属性列表（表的字段）',
  `attribute_alias` varchar(255) NOT NULL DEFAULT '' COMMENT '属性别名定义',
  `list_grid` text COMMENT '列表定义',
  `list_row` smallint(2) unsigned NOT NULL DEFAULT '10' COMMENT '列表数据长度',
  `search_key` varchar(50) NOT NULL DEFAULT '' COMMENT '默认搜索字段',
  `search_list` varchar(255) NOT NULL DEFAULT '' COMMENT '高级搜索的字段',
  `template_list` varchar(255) NOT NULL DEFAULT '' COMMENT '列表显示模板',
  `template_add` varchar(255) NOT NULL DEFAULT '' COMMENT '新增模板',
  `template_edit` varchar(255) NOT NULL DEFAULT '' COMMENT '编辑模板',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `engine_type` varchar(25) NOT NULL DEFAULT 'MyISAM' COMMENT '数据库引擎',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='文档模型表';


-- -----------------------------
-- 表结构 `qinfo_page`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `qinfo_page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `title` varchar(200) DEFAULT NULL,
  `model_id` int(11) NOT NULL,
  `cover_id` int(11) DEFAULT '0',
  `content` text,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='单页';

-- -----------------------------
-- 表内记录 `qinfo_ad_place`
-- -----------------------------
INSERT INTO `qinfo_ad_place` VALUES ('1', '通栏广告', 'banner', '1', '3', '1970', '1970', '1481590565', '1481590575', '', '1');
-- -----------------------------
-- 表内记录 `qinfo_attribute`
-- -----------------------------
INSERT INTO `qinfo_attribute` VALUES ('1', 'uid', '用户ID', '10', 'num', '0', '', '0', '', '1', '0', '1', '1384508362', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('2', 'name', '标识', '40', 'string', '', '同一根节点下标识不重复', '1', '', '1', '0', '1', '1383894743', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('3', 'title', '标题', '80', 'string', '', '文档标题', '1', '', '1', '0', '1', '1383894778', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('4', 'category_id', '所属分类', '10', 'bind', '', '', '1', 'category', '1', '0', '1', '1384508336', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('5', 'description', '描述', '140', 'textarea', '', '', '1', '', '1', '0', '1', '1383894927', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('8', 'model_id', '内容模型ID', '3', 'num', '0', '该文档所对应的模型', '0', '', '1', '0', '1', '1384508350', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('10', 'position', '推荐位', '5', 'select', '0', '多个推荐则将其推荐值相加', '1', '[DOCUMENT_POSITION]', '1', '0', '1', '1383895640', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('11', 'link_id', '外链', '10', 'num', '0', '0-非外链，大于0-外链ID,需要函数进行链接与编号的转换', '1', '', '1', '0', '1', '1383895757', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('12', 'cover_id', '封面', '10', 'image', '0', '0-无封面，大于0-封面图片ID，需要函数处理', '1', '', '1', '0', '1', '1384147827', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('13', 'display', '可见性', '3', 'bool', '1', '', '1', '0:不可见\r\n1:所有人可见', '1', '0', '1', '1386662271', '1383891233', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `qinfo_attribute` VALUES ('14', 'deadline', '截至时间', '10', 'datetime', '0', '0-永久有效', '1', '', '1', '0', '1', '1387163248', '1383891233', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `qinfo_attribute` VALUES ('15', 'attach', '附件数量', '3', 'num', '0', '', '0', '', '1', '0', '1', '1387260355', '1383891233', '', '0', '', 'regex', '', '0', 'function');
INSERT INTO `qinfo_attribute` VALUES ('16', 'view', '浏览量', '10', 'num', '0', '', '1', '', '1', '0', '1', '1383895835', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('17', 'comment', '评论数', '10', 'num', '0', '', '1', '', '1', '0', '1', '1383895846', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('18', 'extend', '扩展统计字段', '10', 'num', '0', '根据需求自行使用', '0', '', '1', '0', '1', '1384508264', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('19', 'level', '优先级', '10', 'num', '0', '越高排序越靠前', '1', '', '1', '0', '1', '1453278679', '1383891233', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('20', 'create_time', '创建时间', '10', 'datetime', '0', '', '1', '', '1', '0', '1', '1383895903', '1383891233', '', '0', '', '', '', '0', '');
INSERT INTO `qinfo_attribute` VALUES ('21', 'update_time', '更新时间', '10', 'text', '0', '', '0', '', '1', '0', '1', '1453278665', '1383891233', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('22', 'status', '数据状态', '4', 'select', '1', '', '0', '-1:删除\r\n0:禁用\r\n1:正常\r\n2:待审核\r\n3:草稿', '1', '0', '1', '1453278660', '1383891233', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('24', 'content', '内容', '', 'weditor', '', '', '1', '', '2', '0', '1', '1453859207', '1453859207', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('25', 'tags', '标签', '20', 'tags', '', '', '1', '', '2', '0', '1', '1453881165', '1453881107', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('26', 'photo_list', '图片列表', '50', 'images', '', '', '1', '', '3', '0', '1', '1454052339', '1454052339', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('27', 'content', '内容', '', 'weditor', '', '', '1', '', '3', '0', '1', '1454052355', '1454052355', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('28', 'title', '标题', '200', 'text', '', '', '1', '', '4', '0', '0', '0', '0', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('29', 'model_id', '模型ID', '11', 'num', '', '', '0', '', '4', '1', '0', '0', '0', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('30', 'cover_id', '封面', '11', 'image', '0', '', '1', '', '4', '0', '0', '0', '0', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('31', 'content', '内容', '', 'weditor', '', '', '1', '', '4', '0', '0', '0', '0', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('32', 'create_time', '创建时间', '11', 'datetime', '', '', '1', '', '4', '0', '0', '0', '0', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('33', 'update_time', '更新时间', '11', 'datetime', '', '', '1', '', '4', '0', '0', '0', '0', '', '0', '', '0', '', '0', '0');
INSERT INTO `qinfo_attribute` VALUES ('34', 'is_top', '是否置顶', '1', 'bool', '0', '', '1', '0:否\r\n1:是', '1', '0', '0', '1466041260', '1466041226', '', '0', '', '0', '', '0', '0');
-- -----------------------------
-- 表内记录 `qinfo_category`
-- -----------------------------
INSERT INTO `qinfo_category` VALUES ('1', 'news', '新闻', '0', '0', '10', '', '', '', '', '', '', '', '', '2', '2,1', '0', '0', '1', '0', '0', '1', 'null', '1379474947', '1481590776', '1', '0', '');
INSERT INTO `qinfo_category` VALUES ('2', 'company_news', '国内', '1', '1', '10', '', '', '', '', '', '', '', '2,3', '2', '2,1,3', '0', '1', '1', '0', '1', '1', '', '1379475028', '1481590794', '1', '0', '');
-- -----------------------------
-- 表内记录 `qinfo_channel`
-- -----------------------------
INSERT INTO `qinfo_channel` VALUES ('1', '0', '网站首页', 'index/index/index', '1', 'home', '', '', '', 'home', '1379475111', '1464490544', '1', '0');
INSERT INTO `qinfo_channel` VALUES ('2', '0', '新闻资讯', 'article/list/1', '1', 'article', '', '', '', 'article', '1379475111', '1464490544', '1', '0');
-- -----------------------------
-- 表内记录 `qinfo_link`
-- -----------------------------
INSERT INTO `qinfo_link` VALUES ('1', '1', '齐信软件', 'http://www.qinfo360.com', '0', '', '0', '0', '1481589667', '0', '1');
-- -----------------------------
-- 表内记录 `qinfo_model`
-- -----------------------------
INSERT INTO `qinfo_model` VALUES ('1', 'document', '通用模型', '0', '', '', '1', '1', '{\"1\":[\"17\",\"16\",\"19\",\"20\",\"14\",\"13\",\"4\",\"3\",\"2\",\"5\",\"12\",\"11\",\"10\"]}', '1:基础,2:扩展', '1,7,8,9,10,2,11,12,13,3,4,14,25,15,5,6,23,22,24', '', '', 'id:ID\r\ntitle:标题\r\nuid:发布人|get_username\r\ncreate_time:创建时间|time_format\r\nupdate_time:更新时间|time_format\r\nstatus:状态|get_content_status', '10', '', '', '', '', '', '1450088499', '1454054412', '1', 'MyISAM');
INSERT INTO `qinfo_model` VALUES ('2', 'article', '文章', '1', 'file-word-o', '', '0', '1', '{\"1\":[\"34\",\"3\",\"2\",\"4\",\"25\",\"12\",\"5\",\"24\"],\"2\":[\"11\",\"10\",\"13\",\"19\",\"17\",\"16\",\"14\",\"20\"]}', '1:基础,2:扩展', '', '', '', 'id:ID\r\ntitle:标题\r\nuid:发布人|get_username\r\ncreate_time:创建时间|time_format\r\nupdate_time:更新时间|time_format\r\nstatus:状态|get_content_status', '10', '', '', '', '', '', '1453859167', '1479464211', '1', 'MyISAM');
INSERT INTO `qinfo_model` VALUES ('3', 'photo', '图片', '1', 'file-image-o', '', '0', '1', '{\"1\":[\"3\",\"2\",\"4\",\"12\",\"26\",\"5\",\"27\",\"55\"],\"2\":[\"11\",\"10\",\"19\",\"13\",\"16\",\"17\",\"14\",\"20\"]}', '1:基础,2:扩展', '', '', '', 'id:ID\r\ntitle:标题\r\nuid:发布人|get_username\r\ncreate_time:创建时间|time_format\r\nupdate_time:更新时间|time_format\r\nstatus:状态|get_content_status', '10', '', '', '', '', '', '1454052310', '1467019679', '1', 'MyISAM');
INSERT INTO `qinfo_model` VALUES ('4', 'page', '单页', '2', 'file-text-o', '', '0', '1', '{\"1\":[\"28\",\"30\",\"31\",\"32\",\"33\"]}', '1:基础', '', '', '', 'id:ID\r\ntitle:标题\r\nupdate_time:更新时间|time_format', '10', '', '', '', '', '', '1456296668', '1481591341', '1', 'MyISAM');
