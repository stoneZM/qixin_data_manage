<?php
// +----------------------------------------------------------------------
// | QinfoCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.qinfo360.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <register@qinfo360.com> <http://www.qinfo360.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\controller\Admin;

class Uploads extends Admin {

    /**
     * 首页
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 文件上传
     */
    public function upload()
    {
        $files = $this->request->file('file');
        $insert = [];
        foreach ($files as $file) {
            $path = ROOT_PATH . 'uploads/picture/';
          
            $info = $file->move($path);
            if ($info) {
                $data[] = $this->request->root() . '/uploads/picture/' . $info->getSaveName();
                $insert[] = [
                    'cate'     => 3,
                    'name'     => $data[] = $this->request->root() . '/uploads/picture/' . $info->getSaveName(),
                    'original' => $info->getInfo('name'),
                    'domain'   => '',
                    'type'     => $info->getInfo('type'),
                    'size'     => $info->getInfo('size'),
                    'mtime'    => time(),
                ];
            } else {
                $error[] = $file->getError();
            }
        }
       // Db::name('File')->insertAll($insert);

        return ajax_return($data);
    }

    /**
     * 远程图片抓取
     */
    public function remote()
    {
        $url = $this->request->post('url');
        // validate
        $name = ROOT_PATH . 'uploads/picture/' . get_random();
        $name = \File::downloadImage($url, $name);

        $ret = $this->request->root() . '/uploads/picture/' . basename($name);

        return ajax_return(['url' => $ret], '抓取成功');
    }

    /**
     * 图片列表
     */
    public function listImage()
    {
        $page = $this->request->param('p', 1);
        if ($this->request->param('count')) {
            $ret['count'] = db('File')->where('')->count();
			
        }
        $ret['list'] = db('File')->where('')->page($page, 10)->select();

        return ajax_return($ret);
    }
	
}