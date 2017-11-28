<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original Author <author@example.com>                        |
// |          Your Name <you@example.com>                                 |
// +----------------------------------------------------------------------+
//
// $Id:$
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 13:44
 */
namespace app\mount\controller;
use app\cdp\model\CdpSnap;
use app\common\controller\Admin;
use app\device\model\ComputeVirtual;
use app\mount\model\Mount;

class Manage extends Admin {

    public function index() {
        $device = db('cdp')->where(array(
            'status' => 1
        ))->field(array(
            'device_id'
        ))->select();
        $source_device = array();
        $field = array(
            'id',
            'alias',
            'ip',
            'unique_id',
            'harddisk_info'
        );
        $device_model = db('device');
        foreach ($device as $k => $v) {
            $device = $device_model->where(array(
                'id' => $v['device_id']
            ))->field($field)->find();
            if (!$device) {
                continue;
            }
            $device['disk_size'] = Mount::getHarddiskSize($device['harddisk_info']);
            $device['task'] = Mount::get_task($device['id']);
            unset($device['harddisk_info']);
            $source_device[] = $device;
        }

        $field = array(
            'id',
            'source_ip',
            'snap_id',
            'status',
            'create_time',
            'config'
        );
        $task_data = db('mount')->field($field)->paginate(15);
        foreach ($task_data as $k => $v) {
            $v['is_temp'] = json_decode($v['config'], true) ['is_temp'];
            $task_data[$k] = $v;
        }
        $data = array(
            'data' => $task_data,
            'page' => $task_data->render() ,
        );
        #$this->assign($source_device);
        $this->assign('sourcedevice',$source_device);
        $this->assign($data);
        return $this->fetch();
    }

    public function add_task() {
        $device_id = input('source_device');
        $snap_id = input('snap');
        $snap_id = explode('_',$snap_id);
        $snap_id = $snap_id[0];

        if(!$device_id) {
            return $this->error(lang('parameter_error'));
        }

        if(!$snap_id) {
            return $this->error(lang('parameter_error'));
        }

        $model = model('device/device');
        $source_info =  Mount::info_filter($model->getDeviceInfo($device_id));
        $data['source_ip']= $source_info['ip'];
        $data['snap_id'] = $snap_id;
        // 0 ==> NOT MOUNT, 1 ==> MOUNTED, 2 MOUNTED ==> ERROR
        $data['status'] = 0;
        $data['create_time'] = time();
        $data['config'] = '';
        $insert_id = db('mount')->insert($data, false, true);
        if ($insert_id) {
            return $this->success(lang('task_add_ok'));
        } else {
            return $this->error(lang('task_add_error'));
        }
    }

    public function browse_task() {
        $id = input('id');
        if (!$id) {
            return $this->error(lang('parameter_error'));
        }

        $task = db('mount')->where(array('id' => $id))->find();
        if (!$task) {
            return $this->error(lang('task_not_exist'));
        }

        // 0 ==> NOT MOUNT, 1 ==> MOUNTED, 2 MOUNTED ==> ERROR
        $status = $task['status'];
        $nbd_device = $task['nbd'];
        $urls = array();
        if ($status == 1) {
            $config = json_decode($task['config'], true);
            $urls = $config['urls'];
        }
        $this->assign('status', $status);
        $this->assign('nbd', $nbd_device);
        $this->assign('urls', $urls);
        // print_r($urls);
        // die;
        return $this->fetch('detail');
    }

    public function mount_task() {
        $id = input('id');
        if (!$id) {
            return $this->error(lang('parameter_error'));
        }

        $task = db('mount')->where(array('id' => $id))->find();
        if (!$task) {
            return $this->error(lang('task_does_not_exist'));
        } else if (count(task) > 1) {
            return $this->error(lang('task_not_unique'));
        }

        $data['task_id'] = $id;
        if (Mount::send_mount_snap_message($data)) {
            return $this->success(lang('task_mount_ok'));
        } else {
            return $this->error(lang('task_mount_error'));
        }
        return $this->success(lang('task_mount_ok'));
    }

    public function umount_task() {
        $id = input('id');
        if (!$id) {
            return $this->error(lang('parameter_error'));
        }

        $task = db('mount')->where(array('id' => $id))->find();
        if (!$task) {
            return $this->error(lang('task_does_not_exist'));
        } else if (count(task) > 1) {
            return $this->error(lang('task_not_unique'));
        }

        $data['task_id'] = $id;
        if (Mount::send_umount_snap_message($data)) {
            return $this->success(lang('task_umount_ok'));
        } else {
            return $this->error(lang('task_umount_error'));
        }
    }

    public function delete_task() {
        $id = input('id');
        if (!$id) {
            return $this->error(lang('parameter_error'));
        }

        $task = db('mount')->where(array('id' => $id))->find();
        if (!$task) {
            return $this->error(lang('task') . lang('not_exist'));
        }

        if (db('mount')->where(array('id' => $id))->delete()) {
            return $this->success(lang('task_delete_ok'));
        } else {
            return $this->error(lang('task_delete_error'));
        }
    }

    public function getVmdk() {
        $task_id = input('id');
        if (!$task_id) return json(array(
            'code' => 0,
            'msg' => lang('params_error')
        ));
        $snap_field = array(
            'id',
            'task_id',
            'file_path',
            'create_time',
            'harddisk_id',
            'have_os',
            'group_id',
            'virtual_id'
        );
        $snap = db('cdp_snap')->where(array(
            'task_id' => $task_id,
            'keli_id' => 0
        ))->field($snap_field)->select();
        foreach ($snap as $key => & $value) {
            if ($value['virtual_id'] != 0) {
                $type_info = ComputeVirtual::where(array(
                    'id' => $value['virtual_id']
                ))->column('type');
                $value['virtual_type'] = $type_info[0];
            }
        }
        if (count($snap) == 0) {
            return json(array(
                'code' => 0,
                'msg' => lang('no_snap_vaild')
            ));
        } else {
            $snaps = Mount::handle_snap_list($snap);
            return json(array(
                'code' => 1,
                'data' => $snaps
            ));
        }
    }

    public function getDevice() {
        $id = input('id');
        $source_id = input('sourceId');
        $field = array(
            'id',
            'harddisk_info'
        );
        $device = db('device')->where(array(
            'id' => $id
        ))->field($field)->find();
        $diskSize = db('device')->where(array(
            'id' => $source_id
        ))->field('harddisk_info')->find();
        if (!$device) return json(array(
            'code' => 0,
            'msg' => lang('choose_device')
        ));
        $diskSize = Mount::getHarddiskSize($diskSize['harddisk_info']);
        $device['disk_size'] = $diskSize;
        $device['harddisk_info'] = Mount::handle_harddisk_info($device['harddisk_info']);
        return json(array(
            'code' => 1,
            'data' => $device
        ));
    }
}

