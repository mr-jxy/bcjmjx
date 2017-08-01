<?php
namespace app\admin\controller;
use app\admin\model\user;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
use think\Session;
class Repair extends Common
{
    public function index()
    {
        $user = new User;
        $data = '*';
        $list = $user::userinfo($data,'');
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

            $rids = Request::instance()->Post('rid/a');
            foreach ($rids as $key => $value) {
                $value = intval($value);
                if ($value == 0) {
                    unset($rids[$key]);
                }
            }
            if (empty($rids)) {
                $this->error('负责区域必须选择');
            }
            $rids = implode(',',$rids);

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
                'rid' => $rids,
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
            $aid = Session::get('admin.id');
            if ($aid > 1) {
                $arid = Session::get('admin.rid');
                $where = array(
                    'id' => array(
                        '0' => 'in',
                        '1' => $arid
                        /*'1' => $rids*/
                    )
                );
                $rlist = Db::name('region')->where($where)->select();
            }else{
                $rlist = Db::name('region')->select();
                $rlist = tree($rlist);
                $rlist = printTree($rlist,'|-');
            }
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

                $rids = Request::instance()->Post('rid/a');
                foreach ($rids as $key => $value) {
                    $value = intval($value);
                    if ($value == 0) {
                        unset($rids[$key]);
                    }
                }
                if (empty($rids)) {
                    $this->error('管辖区域必须选择');
                }
                $rids = implode(',',$rids);

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
                    'rid' => $rids,
                    'address' => $address,
                    'contacts' => $contacts,
                    'phone' => $phone,
                    'email' => $email
                );

                $password = Request::instance()->Post('password','','trim');
                if (!empty($password)) {
                    $password = md5($password);
                    $data['password'] = $password;
                }

    			$is = Db::name('repair')->where('id',$id)->update($data);
    			if ($is) {
    				$this->success('修改成功', Url::build('Repair/index'));
    			}else{
    				$this->error('更新失败');die;
    			}
    		}else{
                $aid = Session::get('admin.id');
                if ($aid > 1) {
                    $arid = Session::get('admin.rid');
                    $where = array(
                        'id' => array(
                            '0' => 'in',
                            '1' => $arid
                            /*'1' => $rids*/
                        )
                    );
                    $rlist = Db::name('region')->where($where)->select();
                }else{
                    $rlist = Db::name('region')->select();
                    $rlist = tree($rlist);
                    $rlist = printTree($rlist,'|-');
                }

                $row = Db::name('repair')->where('id',$id)->find();
                $rids = explode(',', $row['rid']);
                $rids_count = count($rids);
                $this->assign('rlist',$rlist);
                $this->assign('rids',$rids);
                $this->assign('rids_count',$rids_count-1);
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
