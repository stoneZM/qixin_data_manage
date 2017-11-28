<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/4
 * Time: 15:09
 */

namespace app\cdp\model;
use think\Model;

class CdpKeli extends Model
{


    /**************日志信息*********************/
    static function addKeliLog($device_id,$task_time,$result,$desc=''){

        $extent['task_time'] = $task_time;
        $extend['color'] = $result?"green":"red";
        $extend['result'] = $result?"成功":'失败';
        $extend['desc'] = $desc;
        action_log('add_keli', 'cdp', $device_id, session('user_auth.uid'),$extend);
    }



}