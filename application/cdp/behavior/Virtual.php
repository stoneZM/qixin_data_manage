<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/2
 * Time: 13:05
 */

namespace app\cdp\behavior;
use app\cdp\model\CdpSnap;


class Virtual{


    public function beforeCreateVirtual(&$params){

      // 检查快照合并是否已完成
        if(!is_array($params)){
            return false;
        }
        $item = end($params);
        $sub_task_id = $item['sub_task_id'];
        $res = CdpSnap::get(['sub_task_id'=>$sub_task_id,'type'=>3]);

        if(is_null($res))
            return true;
        else
            return false;
    }

}