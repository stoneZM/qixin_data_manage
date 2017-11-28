<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/4
 * Time: 14:11
 */

namespace app\cdp\model;
use think\Model;

class CdpConfig extends Model
{

    /*****************日志*******************************/
    static function addConfigLog($device_id,$result,$type,$ip,$desc=''){

        $extend['color'] = $result?'green':"red";
        $extend['result'] = $result?'成功':"失败";
        $extend["desc"] = $desc;
        $extend['ip'] = $ip;
        $extend['type'] = $type==0?'接管':'演练';
        $action_name = $type==0?"add_auto_takeover_config":"add_auto_practise_config";
        $model_name = $type==0?"cdp":"practise";
        action_log($action_name, $model_name,$device_id, session('user_auth.uid'),$extend);

    }

    static function editConfigLog($device_id,$result,$type,$ip,$desc=''){

        $extend['color'] = $result?'green':"red";
        $extend['result'] = $result?'成功':"失败";
        $extend["desc"] = $desc;
        $extend['ip'] = $ip;
        $extend['type'] = $type==0?'接管':'演练';
        $action_name = $type==0?"edit_auto_takeover_config":"edit_auto_practise_config";
        $model_name = $type==0?"cdp":"practise";
        action_log($action_name, $model_name,$device_id, session('user_auth.uid'),$extend);

    }




}