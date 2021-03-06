<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
use think\Session;
class Login extends Controller
{
    public function index()
    {
    	if (Request::instance()->isPost()) {
    		$username = Request::instance()->Post('name','','trim');
    		if (!empty($username)) {
    			$user = Db::name('admin_user')->field('id,password,rid')->where('name', $username)->find();
    			if (!empty($user)) {
    				$password = Request::instance()->Post('password','','trim');
    				if (!empty($password)) {
    					if ($user['password'] != md5($password)) {
    						$this->error('密码错误!');
    					}else{
    						Session::set('admin',$user);
    						return $this->success('登陆成功', Url::build('Index/index'));
    					}
    				}else{
    					$this->error('密码不能为空!');
    				}
    			}else{
    				$this->error('用户不存在!');
    			}
    		}else{
    			$this->error('用户名不能为空!');
    		}
    		$password = Request::instance()->Post('password','','trim');
    	}else{
    		return $this->fetch('login');
    	} 
    }

    public function out(){
    	Session::clear();
    	return $this->success('退出成功', Url::build('Login/index'));
    }
}
