<?php
namespace app\admin\controller;
use app\admin\model\user;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
use think\Session;
class Set extends Common
{
    public function index(){
    	$id = Session::get('admin.id');
    	if (!empty($id) && $id > 0) {
    		if (Request::instance()->isPost()) {
                $data = array();
    			$oldpassword = Request::instance()->Post('oldpassword','','trim');
                if (!empty($oldpassword)) {
                    $oldpassword = md5($oldpassword);
                    $row = Db::name('admin_user')->where('password',$oldpassword)->find();
                    if (empty($row)) {
                        $this->error('旧密码不正确');die;
                    }
                }else{
                    $this->error('旧密码不能为空');die;
                }

                $password = Request::instance()->Post('password','','trim');
                if (!empty($password)) {
                    $password = md5($password);
                    $data = array(
                        'password' => $password
                    );
                    $is = Db::name('admin_user')->where('id',$id)->update($data);
                    if ($is) {
                        Session::clear();
                        return $this->success('请重新登陆', Url::build('Login/index'));
                    }else{
                        $this->error('更新失败');die;
                    }
                }else{
                    $this->error('新密码不能为空');die;
                }
    		
    		}else{
    			return $this->fetch();
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }
}
