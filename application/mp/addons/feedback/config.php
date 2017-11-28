<?php
/*return array(
    'role'=>array(               //配置在表单中的键名 ,这个会是config[recommendUser]
        'title'=>'设置初始身份:',           //表单的文字
        'type'=>'text',                   //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(                  //select 和radion、checkbox的子选项
            'value'=>'',

        ),
        'value'=>'1',
        'tip'=>'输入初始身份的ID',                     //表单的默认值
    ),

    'type'=>array(
        'title'=>'开启同步登陆：',
        'type'=>'checkbox',
        'options'=>array(
            'Qq'=>'Qq',
            'Sina'=>'Sina',
            'Weixin'=>'Weixin',
        ),
    ),
    'meta'=>array(//配置在表单中的键名 ,这个会是config[title]
        'title'=>'接口验证代码：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'需要在Meta标签中写入验证信息时，拷贝代码到这里。'
    ),
    'bind'=>array(//配置在表单中的键名 ,这个会是config[title]
        'title'=>'是否开启帐号绑定：',//表单的文字
        'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(
            '1'=>'是',
            '0'=>'否',
        ),
        'value'=>'0',
        'tip'=>'不开启则跳过与本地帐号绑定过程，建议审核时关闭绑定。'
    ),

    'group'=>array(
        'type'=>'group',
        'options'=>array(
            'Qq'=>array(
                'title'=>'QQ配置',
                'options'=>array(
                    'QqKEY'=>array(
                        'title'=>'QQ互联APP ID：',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'申请地址：http://connect.qq.com',
                    ),
                    'QqSecret'=>array(
                        'title'=>'QQ互联APP KEY：',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'申请地址：http://connect.qq.com',
                    )
                ),
             ),
            'Sina'=>array(
                'title'=>'新浪配置',
                'options'=>array(

                    'SinaKEY'=>array(
                        'title'=>'新浪App Key：',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'申请地址：http://open.weibo.com/',
                    ),
                    'SinaSecret'=>array(
                        'title'=>'新浪App Sercet：',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'申请地址：http://open.weibo.com/',
                    )

                ),

            ),
            'Weixin'=>array(
                'title'=>'微信配置',
                'options'=>array(

                    'WeixinKEY'=>array(
                        'title'=>'微信App Key：',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'申请地址：https://open.weixin.qq.com/',
                    ),
                    'WeixinSecret'=>array(
                        'title'=>'微信App Sercet：',
                        'type'=>'text',
                        'value'=>'',
                        'tip'=>'申请地址：https://open.weixin.qq.com/',
                    )

                ),

            )
        )
    ),



);

*/



return array(
	'respond_rule' => 1,
	'setting' => 1,
	'setting_list' => array(
		'top_title' => array(
			'title' => '浏览器标题',
			'type' => 'text',
			'placeholder' => '意见反馈'
		),
		'page_title' => array(
			'title' => '页面标题',
			'type' => 'text',
			'placeholder' => '意见反馈'
		),
		'need_name' => array(
			'title' => '是否需要填写姓名',
			'type' => 'radio',
			'options' => array(
				0 => '不需要',
				1 => '需要'
			)
		),
		'need_contact' => array(
			'title' => '是否需要填写联系方式',
			'type' => 'radio',
			'options' => array(
				0 => '不需要',
				1 => '需要'
			)
		),
		'contact_type' => array(
			'title' => '需要填写的联系方式',
			'type' => 'radio',
			'options' => array(
				0 => '手机号',
				1 => 'QQ号',
				2 => '微信号',
				3 => '邮箱'
			),
			'tip' => '开启需要填写联系方式后此选项才起作用'
		)
	),
	'entry' => 1,
	'entry_list' => array(
		'index' => '反馈入口'
	),
	'menu' => 1,
	'menu_list' => array(
		'lists' => '反馈列表'
	)
);

