<?php
namespace app\admin\controller;
use app\admin\model\user;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
class Repair extends Common
{
    public function index()
    {
        $user = new User;
        $list = $user::userinfo();
    	$this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
    	if (Request::instance()->isPost()) {
    		$username = Request::instance()->Post('username','','trim');
            if (empty($username)) {
                $this->error('名称不能为空');
            }

            $password = Request::instance()->Post('password','','trim');
            if (empty($password)) {
                $this->error('密码不能为空');
            }

            $rid = Request::instance()->Post('rid','','intval');
            if ($rid == 0) {
                $this->error('地区必须选择');
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

            $data = array(
                'username' => $username,
                'password' => md5($password),
                'rid' => $rid,
                'address' => $address,
                'contacts' => $contacts,
                'phone' => $phone,
                'email' => $email
            );
            Db::name('repair')->insert($data);
            if (Db::name('repair')->getLastInsID() > 0) {
                $this->success('添加成功', Url::build('Repair/index'));
            }else{
                $this->error('添加失败');
            }
    	}else{
            $rlist = Db::name('region')->select();
            $rlist = tree($rlist);
            $rlist = printTree($rlist,'|-');
            $this->assign('rlist',$rlist);
    		return $this->fetch();
    	}
    }

    public function save(){
    	$id = Request::instance()->param('id','','intval');
    	if (!empty($id) && $id > 0) {
    		if (Request::instance()->isPost()) {
    			$username = Request::instance()->Post('username','','trim');
                if (empty($username)) {
                    $this->error('名称不能为空');
                }

                $rid = Request::instance()->Post('rid','','intval');
                if ($rid == 0) {
                    $this->error('地区必须选择');
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
    			
    			$data = array(
                    'username' => $username,
                    'rid' => $rid,
                    'address' => $address,
                    'contacts' => $contacts,
                    'phone' => $phone,
                    'email' => $email
                );
    			$is = Db::name('repair')->where('id',$id)->update($data);
    			if ($is) {
    				$this->success('修改成功', Url::build('Repair/index'));
    			}else{
    				$this->error('更新失败');die;
    			}
    		}else{
                $rlist = Db::name('region')->select();
                $rlist = tree($rlist);
                $rlist = printTree($rlist,'|-');
                $row = Db::name('repair')->where('id',$id)->find();
                $this->assign('rlist',$rlist);
    			$this->assign('row',$row);
    			return $this->fetch();
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }

    public function del(){
    	$id = Request::instance()->param('id','','intval');
    	if (!empty($id) && $id > 0) {
    		$is = Db::name('repair')->where('id',$id)->delete();
    		if ($is == 0) {
    			$this->error('删除失败');die;
    		}else{
    			$this->success('删除成功', Url::build('Repair/index'));
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }

    
}
