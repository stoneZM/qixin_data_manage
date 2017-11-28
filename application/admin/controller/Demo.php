<?php
/**
 * tpAdmin [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://tpadmin.yuan1994.com/
 * @copyright 2016 yuan1994 all rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

//------------------------
// 演示示例
//-------------------------

namespace app\admin\controller;
use app\common\controller\Admin;
use think\Db;
use think\Exception;
use think\Request;

class Demo extends Admin
{
	    /**
     * Excel一键导出
     */
    public function index()
    {
		$this->setMeta('功能演示');
        return $this->view->fetch();
    }
    /**
     * Excel一键导出
     */
    public function excel()
    {
        if ($this->request->isPost()) {
            $header = ['用户ID', '登录IP', '登录地点', '登录浏览器', '登录操作系统', '登录时间'];
            $data = Db::name("member")->field("uid", true)->order("uid desc")->limit(20)->select();
            if ($error = \Excel::export($header, $data, "示例Excel导出", '2007')) {
                throw new Exception($error);
            }
        } else {
			$this->setMeta('导出Excel');
            return $this->view->fetch();
        }
    }

    /**
     * 下载文件
     * @return mixed
     */
    public function download()
    {
        if ($this->request->param('file')) {
            return \File::download("README.md");
        } else {
			$this->setMeta('下载文件');
            return $this->view->fetch();
        }
    }

    /**
     * 下载远程图片
     * @return mixed
     */
    public function downloadImage()
    {
        if (Request::instance()->isPost()) {
            $url = $this->request->post("url");
            if (substr($url, 0, 4) != "http") {
                return ajax_return_adv_error("url非法");
            }
            $name = "uploads/tmp/" . get_random();
            $filename = \File::downloadImage($url, $name);
            if (!$filename) {
                return ajax_return_adv_error($filename);
            } else {
                $url = '/'.$this->request->root() . $filename;
                return ajax_return_adv("下载成功", '', "图片下载成功，<a href='{$url}' target='_blank' class='c-blue'>点击查看</a><br>{$url}");
            }
        } else {
			$this->setMeta('远程图片下载');
            return $this->view->fetch();
        }
    }

    /**
     * 发送邮件
     * @return mixed
     */
    public function mail()
    {
        if ($this->request->isPost()) {
            $receive = $this->request->post("receiver");
            $result = $this->validate(
                ['receiver' => $receive],
                ['receiver|收件人' => 'require|email']
            );
            if ($result !== true) {
                return ajax_return_adv_error($result);
            }
            $html = "<p>这是一封来自qinfo的测试邮件，请勿回复</p><p><br></p><p>该邮件由访问发送，本站不承担任何责任，如有骚扰请屏蔽此邮件地址</p>";
            $result = \Mail::instance()->mail($receive, $html, "测试邮件");
            if ($result !== true) {
                return ajax_return_adv_error(\Mail::instance()->getError());
            } else {
                return ajax_return_adv("邮件发送成功，请注意查收", '');
            }
        } else {
			$this->setMeta('发送邮件');
            return $this->view->fetch();
        }
    }

    /**
     * 百度编辑器
     * @return mixed
     */
    public function ueditor()
    {	
		$this->setMeta('百度编辑器');
        return $this->view->fetch();
    }

    /**
     * 七牛上传
     * @return mixed
     */
    public function qiniu()
    {
        if ($this->request->isPost()) {
           /* return '<script>parent.layer.alert("仅做演示")</script>';*/
            $result = \Qiniu::instance()->upload();
			if (empty($result[0])) {
				return  ajax_return_adv_error('上传失败');
			} else {
				$url = 'http://ohc7jz1o4.bkt.clouddn.com/'.$result[0]['key'];
				return ajax_return_adv("上传成功", '', "图片上传成功，<a href='{$url}' target='_blank' class='c-blue'>点击查看</a><br>{$url}");
			}
        } else {
			$this->setMeta('七牛上传');
            return $this->view->fetch();
        }
    }

    /**
     * ID加密
     * @return mixed
     */
    public function hashids()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post("id");
            $hashids = \Hashids\Hashids::instance(8, "tpadmin");
            $encode_id = $hashids->encode($id); //加密
            $decode_id = $hashids->decode($encode_id); //解密
            return ajax_return_adv("操作成功", '', false, '', '', ['encode' => $encode_id, 'decode' => $decode_id]);
        } else {
			$this->setMeta('ID加密');
            return $this->view->fetch();
			
        }
    }

    /**
     * 丰富弹层
     */
    public function layer()
    {
		$this->setMeta('丰富弹层');
        return $this->view->fetch();
    }

    /**
     * 表格溢出
     */
    public function tableFixed()
    {
		$this->setMeta('表格溢出');
        return $this->view->fetch();
    }

    /**
     * 图片上传回调
     */
    public function imageUpload()
    {
		$this->setMeta('图片上传');
        return $this->view->fetch();
    }

    /**
     * 二维码生成
     */
    public function qrcode()
    {
		$this->setMeta('二维码');
        return $this->view->fetch();
    }
}