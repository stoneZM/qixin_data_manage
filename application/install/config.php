<?php
return array(

	'view_replace_str' => array(
		'__ADDONS__'           => BASE_PATH . '/addons',
		'__PUBLIC__'           => BASE_PATH . '/public',
		'__STATIC__'           => BASE_PATH . '/application/admin/static',
		'__IMG__'              => BASE_PATH . '/application/admin/static/images',
		'__CSS__'              => BASE_PATH . '/application/admin/static/css',
		'__JS__'               => BASE_PATH . '/application/admin/static/js',
		'__COMPANY__'          => config('systemconfig.site_corporate_name') ?: '齐信软件科技（上海）有限公司',
		'__NAME__'             => config('systemconfig.site_title') ?: '齐信数据管理平台',
		'__COMPANY_WEBSITE__' => config('systemconfig.site_url') ?: 'www.qinfo360.cn',
		'__WEBSITE__' => config('systemconfig.site_url') ?: 'www.qinfo360.cn',
	),
);