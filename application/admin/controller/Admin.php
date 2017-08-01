<?php
namespace app\admin\controller;
use think\Loader;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
class Admin extends Common
{
    public function index()
    {
        $adminModel = Loader::model('Admin_user');
        $list = $adminModel->field('id,name,role')->select();
    	$this->assign('list',$list);
        return $this->fetch();
    }

    public function add()
    {
        if (Request::instance()->isPost()) {
            $adminModel = Loader::model('Admin_user');
            $name = Request::instance()->Post('name','','trim');
            $is_name = $adminModel->field('id')->where('name',$name)->find();
            if (!empty($is_name)) {
                $this->error('账户名重复');
            }
            $password = Request::instance()->Post('password','','trim');
            if (empty($password)) {
                $this->error('密码不能为空');
            }else{
                $password = md5($password);
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

            $real_name = Request::instance()->Post('real_name','','trim');
            if (empty($real_name)) {
                $this->error('真实姓名不能为空');
            }
            $phone = Request::instance()->Post('phone','','trim');
            if (empty($phone)) {
                $this->error('手机号不能为空');
            }else{
                if (!isPhone($phone)) {
                    $this->error('手机号格式不正确');
                }
            }

            $email = Request::instance()->Post('email','','trim');
            if (empty($email)) {
                $this->error('邮箱不能为空');
            }

            $role = Request::instance()->Post('role','','intval');
            if ($role == 0) {
                $this->error('角色必须选择');
            }
            $data = array(
                'name' => $name,
                'password' => $password,
                'rid' => $rids,
                'real_name' => $real_name,
                'phone' => $phone,
                'email' => $email,
                'role' => $role
            );
            $adminModel->insert($data);
            if ($adminModel->getLastInsID() > 0) {
                $this->success('添加成功', Url::build('Admin/index'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $region = Db::name('region')->select();
            $region = tree($region);
            $region = printTree($region,'|-');
            $this->assign('region',$region);
            $role = Db::name('role')->select();
            $this->assign('role',$role);
            return $this->fetch();
        }
    }

    public function save()
    {
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            if (Request::instance()->isPost()) {
                $adminModel = Loader::model('Admin_user');

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

                $real_name = Request::instance()->Post('real_name','','trim');
                if (empty($real_name)) {
                    $this->error('真实姓名不能为空');
                }
                $phone = Request::instance()->Post('phone','','trim');
                if (empty($phone)) {
                    $this->error('手机号不能为空');
                }else{
                    if (!isPhone($phone)) {
                        $this->error('手机号格式不正确');
                    }
                }

                $email = Request::instance()->Post('email','','trim');
                if (empty($email)) {
                    $this->error('邮箱不能为空');
                }

                $role = Request::instance()->Post('role','','intval');
                if ($role == 0) {
                    $this->error('角色必须选择');
                }

                $password = Request::instance()->Post('password','','trim');
                if (!empty($password)) {
                    $password = md5($password);
                    $data = array(
                        'password' => $password,
                        'rid' => $rids,
                        'real_name' => $real_name,
                        'phone' => $phone,
                        'email' => $email,
                        'role' => $role
                    );
                }else{
                    $data = array(
                        'rid' => $rids,
                        'real_name' => $real_name,
                        'phone' => $phone,
                        'email' => $email,
                        'role' => $role
                    );
                }
                
                
                $is = $adminModel->where('id',$id)->update($data);
                if ($is) {
                    $this->success('编辑成功', Url::build('Admin/index'));
                }else{
                    $this->error('编辑失败');die;
                }
            }else{
                $row = Db::name('admin_user')->where('id', $id)->find();
                $rids = explode(',', $row['rid']);
                $rids_count = count($rids);
                $region = Db::name('region')->select();
                $region = tree($region);
                $region = printTree($region,'|-');
                $role = Db::name('role')->select();
                $this->assign('rids',$rids);
                $this->assign('rids_count',$rids_count-1);
                $this->assign('row',$row);
                $this->assign('region',$region);
                $this->assign('role',$role);
                return $this->fetch();
            }
        }else{
            $this->error('参数错误');die;
        }
    }

    public function del(){
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 1) {
            $is = Db::name('admin_user')->where('id',$id)->delete();
            if ($is == 0) {
                $this->error('删除失败');die;
            }else{
                $this->success('删除成功', Url::build('Admin/index'));
            }
        }else{
            $this->error('参数不合法');
        }
    }

    public function role()
    {
        $roleModel = Loader::model('Role');
        $list = $roleModel->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function role_add()
    {
        if (Request::instance()->isPost()) {
            $roleModel = Loader::model('Role');
            $title = Request::instance()->Post('title','','trim');
            $is_tietle = $roleModel->field('id')->where('title',$title)->find();
            if (!empty($is_tietle)) {
                $this->error('角色名重复');
            }
            $rules = Request::instance()->Post('rules/a');
            $rules = implode(',',$rules);
            $data = array(
                'title' => $title,
                'rules' => $rules
            );
            $roleModel->insert($data);
            if ($roleModel->getLastInsID() > 0) {
                $this->success('添加成功', Url::build('Admin/role'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $ruleModel = Loader::model('Rule');
            $list = $ruleModel->select();
            $list = tree($list);
            $this->assign('list',$list);
            return $this->fetch();
        }
    }

    public function role_save()
    {
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            if (Request::instance()->isPost()) {
                $roleModel = Loader::model('Role');
                $title = Request::instance()->Post('title','','trim');
                $is_tietle = $roleModel->field('id')->where('title',$title)->find();
                if (!empty($is_tietle)) {
                    $iss_tietle = $roleModel->field('title')->where('id',$id)->find();
                    if ($iss_tietle['title']!=$title) {
                        $this->error('角色名重复');
                    }                  
                }
                $rules = Request::instance()->Post('rules/a');
                $rules = implode(',',$rules);
                $data = array(
                    'title' => $title,
                    'rules' => $rules
                );
                $is = $roleModel->where('id',$id)->update($data);
                if ($is) {
                    $this->success('编辑成功', Url::build('Admin/role'));
                }else{
                    $this->error('编辑失败');die;
                }
            }else{
                $roleModel = Loader::model('Role');
                $row = $roleModel->where('id',$id)->find();
                $rules = explode(',', $row['rules']);
                $ruleModel = Loader::model('Rule');
                $list = $ruleModel->select();
                $list = tree($list);
                $this->assign('row',$row);
                $this->assign('rules',$rules);
                $this->assign('list',$list);
                return $this->fetch();
            }
        }else{
            $this->error('参数不合法');die;
        }
        
    }

    public function role_del(){
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            $is = Db::name('role')->where('id',$id)->delete();
            if ($is == 0) {
                $this->error('删除失败');die;
            }else{
                $this->success('删除成功', Url::build('Admin/role'));
            }
        }else{
            $this->error('参数不合法');
        }
    }

    public function rule()
    {
        $ruleModel = Loader::model('Rule');
        $list = $ruleModel->select();
        $list = printTree(tree($list),'|-');
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function rule_add()
    {
        if (Request::instance()->isPost()) {
            $ruleModel = Loader::model('Rule');
            $fid = Request::instance()->Post('fid','','intval');
            $title = Request::instance()->Post('title','','trim');
            $is_tietle = $ruleModel->field('id')->where('title',$title)->find();
            if (!empty($is_tietle)) {
                $this->error('权限名称重复');
            }
            $name = Request::instance()->Post('name','','trim');
            $is_name = $ruleModel->field('id')->where('name',$name)->find();
            if (!empty($is_tietle)) {
                $this->error('权限链接重复');
            }
            $data = array(
                'title' => $title,
                'name' => $name,
                'fid' => $fid
            );
            $ruleModel->insert($data);
            if ($ruleModel->getLastInsID() > 0) {
                $this->success('添加成功', Url::build('Admin/rule'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $ruleModel = Loader::model('Rule');
            $list = $ruleModel->select();
            $list = printTree(tree($list),'|-','title');
            $this->assign('list',$list);
            return $this->fetch();
        }
    }

    public function rule_save()
    {
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            if (Request::instance()->isPost()) {
                $ruleModel = Loader::model('Rule');
                $fid = Request::instance()->Post('fid','','intval');
                $title = Request::instance()->Post('title','','trim');

                $is_tietle = $ruleModel->field('id')->where('title',$title)->find();
                if (!empty($is_tietle)) {
                    $iss_tietle = $ruleModel->field('title')->where('id',$id)->find();
                    if ($iss_tietle['title']!=$title) {
                        $this->error('权限名称重复');
                    }                  
                }

                
                /*$is_tietle = $ruleModel->field('id')->where('title',$title)->find();
                if (!empty($is_tietle)) {
                    $this->error('权限名称重复');
                }*/
                $name = Request::instance()->Post('name','','trim');
                /*$is_name = $ruleModel->field('id')->where('name',$name)->find();
                if (!empty($is_name)) {
                    $this->error('权限链接重复');
                }*/
                $data = array(
                    'title' => $title,
                    'name' => $name,
                    'fid' => $fid
                );
                $is = $ruleModel->where('id',$id)->update($data);
                if ($is) {
                    $this->success('编辑成功', Url::build('Admin/rule'));
                }else{
                    $this->error('编辑失败');die;
                }
            }else{
                $ruleModel = Loader::model('Rule');
                $row = $ruleModel->where('id',$id)->find();
                $list = $ruleModel->field('id,title,fid')->where('id','<>',$id)->select();
                $list = printTree(tree($list),'|-','title');
                $this->assign('row',$row);
                $this->assign('list',$list);
                return $this->fetch();
            }
        }else{
            $this->error('参数不合法');die;
        }
        
    }

    public function rule_del(){
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            $is = Db::name('rule')->where('id',$id)->delete();
            if ($is == 0) {
                $this->error('删除失败');die;
            }else{
                $this->success('删除成功', Url::build('Admin/rule'));
            }
        }else{
            $this->error('参数不合法');
        }
    }

    
}
