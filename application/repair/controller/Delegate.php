<?php
namespace app\repair\controller;
use think\Controller;
use app\admin\model\user;
use think\Request;
use think\Db;
use think\Url;
use think\Session;
class Delegate extends Common
{
    public function n_list()
    {
        $id = Session::get('repair.id');
        $user = new user;
        $data = 'r.id,r.order_sn,r.mobile,r.type,rl.username';
        $where = array(
            'r.repair' => $id,
            'r.type' => array(
                '0' => '>',
                '1' => 0
            ),
            'r.type' => array(
                '0' => '<>',
                '1' => 4
            )
        );
        $list = $user::n_list($data,$where);
    	$this->assign('list',$list);
        return $this->fetch();
    }

    public function n_save()
    {
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            if (Request::instance()->isPost()) {
                $type = Request::instance()->Post('type','','intval');
                if ($type == 0) {
                    $this->error('进度必须选择');
                }
                if ($type == 4) {
                    $answer = Request::instance()->Post('answer','','trim');
                    if (empty($answer)) {
                        $this->error('请填写回执');
                    }
                    $time = $_SERVER['REQUEST_TIME'];
                }else{
                    $answer = '';
                    $time = '';
                }
                $data = array(
                    'type' => $type,
                    'answer' => $answer,
                    'endtime' => $time
                );
                $is = Db::name('repair_order')->where('id',$id)->update($data);
                if ($is) {
                    $this->success('修改成功', Url::build('Delegate/n_list'));
                }else{
                    $this->error('更新失败');die;
                }
            }else{
                $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id')->field('rl.*,ro.type,ro.answer')->where('rl.id',$id)->find();
                $repair = Db::name('repair')->where('rid',$row['rid'])->select();
                $rlist = Db::name('region')->where('id',$row['rid'])->find();
                
                $this->assign('row',$row);
                $this->assign('repair',$repair);
                $this->assign('rlist',$rlist);
                return $this->fetch();
            }
        }else{
            $this->error('参数不合法');
        }
    }

    public function y_list()
    {
        $id = Session::get('repair.id');
        $user = new user;
        $data = 'r.id,r.order_sn,r.mobile,r.type,rl.username';
        $where = array(
            'r.repair' => $id,
            'r.type' => 4
        );
        $list = $user::n_list($data,$where);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function y_save()
    {
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            if (Request::instance()->isPost()) {
                $answer = Request::instance()->Post('answer','','trim');
                if (empty($answer)) {
                    $this->error('回执不能为空');
                }
                $data = array(
                    'answer' => $answer,
                );
                $is = Db::name('repair_order')->where('id',$id)->update($data);
                if ($is) {
                    $this->success('修改成功', Url::build('Delegate/y_list'));
                }else{
                    $this->error('更新失败');die;
                }
            }else{
                $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id')->field('rl.*,ro.type,ro.answer,ro.endtime')->where('rl.id',$id)->find();
                $repair = Db::name('repair')->where('rid',$row['rid'])->select();
                $rlist = Db::name('region')->where('id',$row['rid'])->find();
                
                $this->assign('row',$row);
                $this->assign('repair',$repair);
                $this->assign('rlist',$rlist);
                return $this->fetch();
            }
        }else{
            $this->error('参数不合法');
        }
    }
}
