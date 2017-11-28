<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\device\model;

use think\Model;

class Index extends Model
{
    // 指定表名,不含前缀
    protected $name = 'device';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
}
