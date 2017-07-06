<?php
namespace app\repair\controller;
use app\admin\model\user;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
use think\Session;
class Set extends Common
{
    public function index(){
    	$id = Session::get('repair.id');
    	if (!empty($id) && $id > 0) {
    		if (Request::instance()->isPost()) {
                $data = array();
    			$password = Request::instance()->Post('password','','trim');
                if (!empty($password)) {
                    $password = md5($password);
                    $data['password'] = $password;
                }

                $address = Request::instance()->Post('address','','trim');
                if (empty($address)) {
                    $this->error('地址不能为空');
                }

                $contacts = Request::instance()->Post('contacts','','trim');
                if (empty($contacts)) {
                    $this->error('联系人不能为空');
                }

                $phone = Request::instance()->Post('phone','','trim');
                if (empty($phone)) {
                    $this->error('联系方式不能为空');
                }

                $email = Request::instance()->Post('email','','trim');
                if (empty($email)) {
                    $this->error('email不能为空');
                }
    			$data['address'] = $address;
                $data['contacts'] = $contacts;
                $data['phone'] = $phone;
                $data['email'] = $email;
    			$is = Db::name('repair')->where('id',$id)->update($data);
    			if ($is) {
    				$this->success('修改成功', Url::build('Set/index'));
    			}else{
    				$this->error('更新失败');die;
    			}
    		}else{
                $row = Db::name('repair')->where('id',$id)->find();
    			$this->assign('row',$row);
    			return $this->fetch();
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }
}
