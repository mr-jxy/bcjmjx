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
        $type = Request::instance()->param('type','','intval');
        $id = Session::get('repair.id');
        $user = new user;
        $data = 'r.id,r.order_sn,r.mobile,r.type,rl.username';
        if ($type == 0) {
            $where = array(
                'r.repair' => $id,
                'r.type' => array(
                    '0' => '>',
                    '1' => 0
                ),
                'r.type' => array(
                    '0' => '<',
                    '1' => 4
                )
            );
        }else{
            $where = array(
                'r.repair' => $id,
                'r.type' => $type
            );
        }
        
        $list = $user::n_list($data,$where);
    	$this->assign('list',$list);
        return $this->fetch();
    }

    public function n_save()
    {
        $id = Request::instance()->param('id','','intval');
        $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id','left')->join('region city','rl.rid = city.id','left')->join('region provinces','city.fid = provinces.id','left')->field('rl.*,ro.order_sn,ro.type,ro.username_note,ro.eq_num_note,ro.answer,city.name as ciname,provinces.name as prname')->where('rl.id',$id)->find();

        if (!empty($id) && $id > 0) {
            if (Request::instance()->isPost()) {
                //图片处理
                $images = Request::instance()->Post('images/a');
                if (!empty($images)) {
                    $images = implode(',', $images);
                }else{
                    $images = '';
                }
                /*$files = Request::instance()->file("image");
                foreach ($files as $value) {
                    $fileinfo = $value->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if ($fileinfo) {
                        $image[] = $fileinfo->getSaveName();
                        $image = implode(',', $image);
                    }else{
                        // 上传失败获取错误信息
                        $this->error($value->getError());die;
                    }
                }*/

                $type = Request::instance()->Post('type','','intval');
                if ($type == 0) {
                    $this->error('进度必须选择');
                }
                if ($type < $row['type']) {
                    $this->error('进度不能越级选择');
                }
                $answer = Request::instance()->Post('answer','','trim');
                if (empty($answer)) {
                    $this->error('请填写说明');
                }
                $log_data = array(
                    'order_id' => $id,
                    'type' => $type,
                    'state' => $answer,
                    'change_time' => $_SERVER['REQUEST_TIME'],
                    'img' => $images
                );
                /*if (!empty($image)) {
                    $log_data['img'] = $image;
                }*/
                Db::name('repair_log')->insert($log_data);

                if ($type == 4) {
                    $time = $_SERVER['REQUEST_TIME'];
                    zendSms($row['phone'],'【北村精密机械】您的订单已经完成，感谢您的支持。'); 
                }else{
                    $time = '';
                }

                $username_note = Request::instance()->Post('username_note','','trim');
                $eq_num_note = Request::instance()->Post('eq_num_note','','trim');
                $data = array(
                    'type' => $type,
                    'username_note' => $username_note,
                    'eq_num_note' => $eq_num_note,
                    'endtime' => $time
                );
                $is = Db::name('repair_order')->where('id',$id)->update($data);
                if ($is) {
                    $this->success('修改成功', Url::build('Delegate/n_list'));
                }else{
                    $this->error('更新失败');die;
                }
            }else{
                //订单产品联动
                if ($row['eq_id'] > 0) {
                    $equipment = Db::name('equipment e')->join('equipment_category ec','e.cid = ec.id')->field('e.name,ec.name as ecname')->where('e.id',$row['eq_id'])->find();
                }else{
                    $equipment['name'] = $row['eq_info'];
                }

                //订单日志
                $repair_log = Db::name('repair_log')->where('order_id',$row['id'])->select();
                foreach ($repair_log as $key => $value) {
                    if ($value['type'] == 1) {
                        $log1 = $value;
                        unset($repair_log[$key]);
                        if (!empty($log1['img'])) {
                            $log1['img'] = explode(',', $log1['img']);
                        }
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

                //图片处理
                if (!empty($row['image'])) {
                    $images = explode(',', $row['image']);
                    $this->assign('images',$images);
                }

                
                $this->assign('row',$row);
                $this->assign('equipment',$equipment);
                return $this->fetch();
            }
        }else{
            $this->error('参数不合法');
        }
    }

    public function imguplod(){
        //图片处理
        $file = Request::instance()->file("fileList");
        $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){   
            $image = $info->getSaveName();
        }else{
            $this->error($value->getError());die;
        }
        return $image;
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
                    'state' => $answer,
                );
                $where = array(
                    'order_id' => $id,
                    'type' => 4
                );
                $is = Db::name('repair_log')->where($where)->update($data);
                if ($is) {
                    $this->success('修改成功', Url::build('Delegate/y_list'));
                }else{
                    $this->error('更新失败');die;
                }
            }else{
                $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id','left')->join('region city','rl.rid = city.id','left')->join('region provinces','city.fid = provinces.id','left')->field('rl.*,ro.order_sn,ro.type,ro.answer,ro.endtime,city.name as ciname,provinces.name as prname')->where('rl.id',$id)->find();

                //订单产品联动
                if ($row['eq_id'] > 0) {
                    $equipment = Db::name('equipment e')->join('equipment_category ec','e.cid = ec.id')->field('e.name,ec.name as ecname')->where('e.id',$row['eq_id'])->find();
                }else{
                    $equipment['name'] = $row['eq_info'];
                }

                //订单日志
                $repair_log = Db::name('repair_log')->where('order_id',$row['id'])->select();
                foreach ($repair_log as $key => $value) {
                    if ($value['type'] == 1) {
                        $log1 = $value;
                        unset($repair_log[$key]);
                        if (!empty($log1['img'])) {
                            $log1['img'] = explode(',', $log1['img']);
                        }

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

                //图片处理
                if (!empty($row['image'])) {
                    $images = explode(',', $row['image']);
                    $this->assign('images',$images);
                }

                $where = array(
                    'order_id' => $row['id'],
                    'type' => 4
                );
                $rowinfo = Db::name('repair_log')->field('state')->where($where)->find();
        
                
                $this->assign('equipment',$equipment);
                $this->assign('row',$row);
                $this->assign('state',$rowinfo['state']);
                return $this->fetch();
            }
        }else{
            $this->error('参数不合法');
        }
    }
}
