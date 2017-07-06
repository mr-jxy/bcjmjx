<?php
namespace app\index\controller;
use think\Controller;
use app\admin\model\user;
use think\Request;
use think\Db;
use think\Url;
use think\Session;
class Slist extends Controller
{
    public function index()
    {
        if (Request::instance()->isPost()) {
            $phone = Request::instance()->param('phone','','trim');
            if (empty($phone)) {
                $this->error('手机号必须输入');
            }else{
                if (!preg_match("/^1[3|4|5|8|7][0-9]\d{4,8}$/", $phone)) {
                    $this->error('手机号格式不正确');die;
                }
            }
            $row = Db::name('repair_order')->field('count(*)')->where('mobile',$phone)->find();
            if ($row['count(*)'] == 0) {
                $this->error('没有查询到订单哦');die;
            }else{
                $this->redirect('Slist/slist', ['phone' => $phone]);
            }
        }else{
            return $this->fetch(); 
        } 
    }

    public function slist()
    {
        $phone = Request::instance()->param('phone','','trim');
        if (empty($phone)) {
            $this->success('参数不正确', Url::build('Slist/index'));
        }else{
            if (!preg_match("/^1[3|4|5|8|7][0-9]\d{4,8}$/", $phone)) {
                $this->success('手机号格式不正确', Url::build('Slist/index'));
            }
        }
        $user = new user;
        $data = 'r.id,r.order_sn,r.mobile,r.type,rl.username';
        $where = array(
            'r.mobile' => $phone
        );
        $list = $user::n_list($data,$where);
        $this->assign('list',$list);
        return $this->fetch(); 
    }

    public function save()
    {
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {   
            $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id')->join('repair r','ro.repair = r.id')->field('rl.*,ro.type,ro.answer,ro.repair,r.address,r.phone as rphone,r.contacts')->where('rl.id',$id)->find();
            if (empty($row['answer'])) {
                $row['answer'] = '暂无回复';
            }
            $rlist = Db::name('region')->where('id',$row['rid'])->find();
            
            $this->assign('row',$row);
            $this->assign('rlist',$rlist);
            return $this->fetch();
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
