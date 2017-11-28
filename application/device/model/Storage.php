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

class Storage extends Model
{
    // 指定表名,不含前缀
    protected $name = 'storage';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';



    static function updateStorageSize(){

        $license = get_license();
        $storage = $license['config_info']['storage'];
        $storage_net_size = $storage['storage_net_size'];
//        $storage_ali_size = $storage['storage_ali_size'];
//        $storage_qiniu_size = $storage['storage_qiniu_size'];
        self::where(array('type'=>1))->update(array('size'=>$storage_net_size));


    }

}
