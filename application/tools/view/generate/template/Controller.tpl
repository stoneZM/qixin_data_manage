<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\[MODULE]\controller[NAMESPACE];
use app\common\controller\Admin;

class [NAME] extends Admin
{
	protected $module_table = '[TABLE]';
    use \app\common\controller\Traits; 
    // 方法黑名单
    protected static $blacklist = [];

    [FILTER]
}