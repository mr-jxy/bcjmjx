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
            $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id','left')->join('repair r','ro.repair = r.id','left')->field('rl.*,ro.order_sn,ro.type,ro.answer,ro.repair,r.address as raddress,r.phone as rphone,r.contacts as rcontacts')->where('rl.id',$id)->find();
            if (empty($row['answer'])) {
                $row['answer'] = '暂无回复';
            }
            $isorder = Db::name('rated')->field('order_id')->where('order_id',$id)->find();
            if (empty($isorder)) {
                $row['rated_type'] = 1;
            }
            //处理图片
            if (!empty($row['image'])) {
                $images = explode(',', $row['image']);
                $this->assign('images',$images);
            }
            //客服的市区
            $rllist = Db::name('region')->where('id',$row['rid'])->find();
            //报修人市区
            $rlist = Db::name('region')->where('id',$row['rid'])->find();
            if ($row['eq_id'] > 0) {
                $equipment = Db::name('equipment')->field('name')->where('id',$row['eq_id'])->find();
                $equipment = $equipment['name'];
            }else{
                $equipment = $row['eq_info'];
            }
            $repair_log = Db::name('repair_log')->where('order_id',$row['id'])->select();
            foreach ($repair_log as $key => $value) {
                if ($value['type'] == 1) {
                    $log1 = $value;
                    unset($repair_log[$key]);
                    $this->assign('log1',$log1);
                }
                if ($value['type'] == 2) {
                    $log2 = $value;
                    unset($repair_log[$key]);
                    if (!empty($log2['img'])) {
                        $log2['img'] = explode(',', $log2['img']);
                    }
                    $this->assign('log2',$log2);
                }
                if ($value['type'] == 3) {
                    $log3 = $value;
                    unset($repair_log[$key]);
                    if (!empty($log3['img'])) {
                        $log3['img'] = explode(',', $log3['img']);
                    }
                    $this->assign('log3',$log3);
                }
                if ($value['type'] == 4) {
                    $log4 = $value;
                    unset($repair_log[$key]);
                    if (!empty($log4['img'])) {
                        $log4['img'] = explode(',', $log4['img']);
                    }
                    $this->assign('log4',$log4);
                }
            }
            //留言处理
            $rated = Db::name('rated')->field('mark,content')->where('order_id',$row['id'])->find();
            if (!empty($rated)) {
                $this->assign('rated',$rated);
            }
            $this->assign('equipment',$equipment);
            $this->assign('row',$row);
            $this->assign('rllist',$rllist);
            $this->assign('rlist',$rlist);
            return $this->fetch();
        }
    }

    public function rated()
    {
        $id = Request::instance()->param('id','','intval');
        $isorder = Db::name('rated')->field('order_id')->where('order_id',$id)->find();
        if (!empty($isorder)) {
            $this->error('请不要重复评价哦');die;
        }
        if (!empty($id) && $id > 0) 
        {
            $row = Db::name('repair_order')->field('id,repair')->where('id',$id)->find();
            if (Request::instance()->isPost()) 
            {
                $fenshu = Request::instance()->param('fenshu','','intval');
                if ($fenshu == 0) {
                    $this->error('请您给本次服务评分');die;
                }

                $content = Request::instance()->Post('content','','trim');
                $data = array(
                    'order_id' => $id,
                    'mark' => $fenshu,
                    'content' => $content
                );

                $is = Db::name('rated')->insert($data);
                if ($is == 1) {
                    $this->success('提交成功，感谢您的评价', Url::build('Slist/index'));
                }else{
                    $this->error('添加失败');
                }
            }else{
                $rname = Db::name('repair')->field('username')->where('id',$row['repair'])->find();
                $this->assign('rname',$rname['username']);
                $this->assign('id',$row['id']);
                return $this->fetch();
            }
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
                $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id','left')->field('rl.*,ro.type,ro.answer,ro.endtime')->where('rl.id',$id)->find();
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
