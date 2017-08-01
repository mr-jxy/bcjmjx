<?php
namespace app\admin\controller;
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
        $user = new user;
        $data = 'r.id,r.order_sn,r.mobile,r.type,rl.username';

        $aid = Session::get('admin.id');
        if ($aid > 1) {
            $arid = Session::get('admin.rid');
            /*$region = Db::name('region')->field('id')->where('fid','in',"$rid")->select();
            $rids = '';
            foreach ($region as $key => $value) {
                $rids.=$value['id'].',';
            }
            $rids = substr($rids,0,-1);*/
            $where = array(
                'r.repair' => '0',
                'r.type' => '0',
                /*'rl.rid' => array(
                    '0' => 'in',
                    '1' => $arid
                    /*'1' => $rids
                )*/
            );
            $where[] = ['exp','FIND_IN_SET(rl.rid,"'.$arid.'")'];
        }else{
            $where = array(
                'r.repair' => '0',
                'r.type' => '0'
            );
        }
        
        $order = 'r.id desc';
        $list = $user::n_list($data,$where,$order);
        //var_dump($list);die;
    	$this->assign('list',$list);
        return $this->fetch();
    }

    public function n_save()
    {
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            if (Request::instance()->isPost()) {
                $cid = Request::instance()->Post('cid','','intval');
				$order_sn = Request::instance()->Post('order_sn','','trim');
				$contacts = Request::instance()->Post('contacts','','trim');
                $userphone = Request::instance()->Post('userphone','','trim');
                if ($cid == 0) {
                    $this->error('报修人必须选择');
                }
                $data = array(
                    'repair' => $cid,
                    'type' => 1
                );
                $is = Db::name('repair_order')->where('id',$id)->update($data);
                if ($is) {
                    $log_data = array(
                        'order_id' => $id,
                        'change_time' => $_SERVER['REQUEST_TIME'],
                        'type' => 1
                    );
                    Db::name('repair_log')->insert($log_data);
                    $rphone = Db::name('repair')->field('contacts,phone,email')->where('id',$cid)->find();
                    if (isPhone($rphone['phone'])) {
                        zendSms($userphone,'【北村精密机械】您的订单已经成功受理，请随时登录微信公众号查询报修进度。');                        
                    }
                    zendEmail($rphone['email'],'您有一个新的报修订单需要处理，订单号为：'.$order_sn.'， 联系人：'.$contacts.'， 联系电话：'.$userphone.'请及时登录系统进行处理。
');
                    $this->success('修改成功', Url::build('Delegate/n_list'));
                }else{
                    $this->error('更新失败');die;
                }
            }else{
                $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id','left')->field('rl.*,ro.order_sn')->where('rl.id',$id)->find();
                $repair = Db::query('select * from bc_repair where find_in_set('.$row['rid'].',rid)');
                $rlist = Db::name('region')->where('id',$row['rid'])->find();
                if ($row['eq_id'] > 0) {
                    $equipment = Db::name('equipment e')->join('equipment_category ec','e.cid = ec.id')->field('e.name,ec.name as ecname')->where('e.id',$row['eq_id'])->find();
                }else{
                    $equipment['name'] = $row['eq_info'];
                }
                if (!empty($row['image'])) {
                    $images = explode(',', $row['image']);
                    $this->assign('images',$images);
                }
                //市区
                $city = Db::name('region')->field('name,fid')->where('id',$row['rid'])->find();
                $this->assign('city',$city);
                //省份
                $provinces = Db::name('region')->field('name')->where('id',$city['fid'])->find();
                $this->assign('provinces',$provinces);

                $this->assign('row',$row);
                $this->assign('repair',$repair);
                $this->assign('rlist',$rlist);
                $this->assign('equipment',$equipment);
                return $this->fetch();
            }
        }else{
            $this->error('参数不合法');
        }
    }

    public function n_del(){
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            $is = Db::name('repair_list')->where('id',$id)->delete();
            if ($is == 0) {
                $this->error('删除失败');die;
            }else{
                Db::name('repair_order')->where('id',$id)->delete();
                $this->success('删除成功', Url::build('Delegate/n_list'));
            }
        }else{
            $this->error('参数不合法');
        }
    }

    public function y_list()
    {
        $type = Request::instance()->param('type','','intval');
        $user = new user;
        $data = 'r.id,r.order_sn,r.mobile,r.repair,r.type,rl.username,rl.rid';

        $aid = Session::get('admin.id');
        if ($aid > 1) {
            $arid = Session::get('admin.rid');
            /*$region = Db::name('region')->field('id')->where('fid','in',"$rid")->select();
            $rids = '';
            foreach ($region as $key => $value) {
                $rids.=$value['id'].',';
            }
            $rids = substr($rids,0,-1);*/
            if ($type == 0) {
                $where = array(
                    'r.repair' => array(
                        '0' => '<>',
                        '1' => 0
                    ),
                    'r.type' => array(
                        '0' => '<>',
                        '1' => 0
                    ),
                    /*'rl.rid' => array(
                        '0' => 'in',
                        '1' => $arid
                        //'1' => $rids
                    )*/
                );
                $where[] = ['exp','FIND_IN_SET(rl.rid,"'.$arid.'")'];
            }else{
                $where = array(
                    'r.type' => $type
                );
            }
        }else{
            if ($type == 0) {
                $where = array(
                    'r.repair' => array(
                        '0' => '<>',
                        '1' => 0
                    ),
                    'r.type' => array(
                        '0' => '<>',
                        '1' => 0
                    )
                );
            }else{
                $where = array(
                    'r.type' => $type
                );
            }
        }
        $list = $user::n_list($data,$where);
        $rname = Db::name('repair')->field('id,username')->select();
        $reg = Db::name('region')->field('id,name')->select();
        foreach ($list as $key => $value) {
            foreach ($rname as $ke => $val) {
                if ($value['repair'] == $val['id']) {
                    $list[$key]['rname'] = $val['username'];
                    break;
                }
            }
            foreach ($reg as $ke => $val) {
                if ($value['rid'] == $val['id']) {
                    $list[$key]['regname'] = $val['name'];
                    break;
                }
            }
        }
        unset($rname);
        unset($reg);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function y_save()
    {
        $id = Request::instance()->param('id','','intval');
        if (!empty($id) && $id > 0) {
            if (Request::instance()->isPost()) {
                $note = Request::instance()->Post('note','','intval');
                if ($note == 1) {
                    $username_note = Request::instance()->Post('username_note','','trim');
                    $eq_num_note = Request::instance()->Post('eq_num_note','','trim');
                    $data = array(
                        'username_note' => $username_note,
                        'eq_num_note' => $eq_num_note
                    );
                    $is = Db::name('repair_order')->where('id',$id)->update($data);
                }else{
                    $cid = Request::instance()->Post('cid','','intval');
                    if ($cid == 0) {
                        $this->error('报修人必须选择');
                    }
                    $data = array(
                        'repair' => $cid,
                        'type' => 1
                    );
                    $is = Db::name('repair_order')->where('id',$id)->update($data);
                }
                if ($is) {
                    $this->success('修改成功', Url::build('Delegate/y_list'));
                }else{
                    $this->error('更新失败');die;
                }               
            }else{
                $row = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id','left')->field('rl.*,ro.order_sn,ro.repair,ro.type,ro.username_note,ro.eq_num_note')->where('rl.id',$id)->find();
                $order_repair = Db::name('repair r')->join('region city','r.rid = city.id','left')->join('region provinces','city.fid = provinces.id','left')->field('r.username,r.address,r.contacts,r.phone,r.email,city.name as ciname,provinces.name as prname')->where('r.id',$row['repair'])->find();

                //订单日志
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

                //订单联动地区
                $row_region = Db::name('region city')->join('region provinces','city.fid = provinces.id','left')->field('city.name as ciname,provinces.name as prname')->where('city.id',$row['rid'])->find();

                //订单产品联动
                if ($row['eq_id'] > 0) {
                    $equipment = Db::name('equipment e')->join('equipment_category ec','e.cid = ec.id')->field('e.name,ec.name as ecname')->where('e.id',$row['eq_id'])->find();
                }else{
                    $equipment['name'] = $row['eq_info'];
                }
                

                //图片处理
                if (!empty($row['image'])) {
                    $images = explode(',', $row['image']);
                    $this->assign('images',$images);
                }

                $repair = Db::query('select * from bc_repair where find_in_set('.$row['rid'].',rid)');
                //$repair = Db::name('repair')->where('rid','find_in_set()',$row['rid'])->select();
                $equipment = Db::name('equipment')->where('id',$row['eq_id'])->find();
                $this->assign('row',$row);
                $this->assign('order_repair',$order_repair);
                $this->assign('row_region',$row_region);
                $this->assign('repair',$repair);
             
                $this->assign('equipment',$equipment);
                return $this->fetch();
            }
        }else{
            $this->error('参数不合法');
        }
    }

    public function pro_add(){
    	if (Request::instance()->isPost()) {
    		$name = Request::instance()->Post('name','','trim');
            $fid = Request::instance()->Post('fid','','intval');
    		if (!empty($name)) {
    			$data = array(
    				'name' => $name,
                    'fid' => $fid
    			);
    			Db::name('region')->insert($data);
    			if (Db::name('region')->getLastInsID() > 0) {
    				$this->success('添加成功', Url::build('Region/pro_list'));
    			}else{
    				$this->error('添加失败');
    			}
    		}else{
    			$this->error('名称不能为空!');
    		}
    	}else{
            $flist = Db::name('region')->where('fid',0)->select();
            $this->assign('flist',$flist);
    		return $this->fetch();
    	}
    }

    public function pro_save(){
    	$id = Request::instance()->param('id','','intval');
    	if (!empty($id) && $id > 0) {
    		if (Request::instance()->isPost()) {
    			$name = Request::instance()->Post('name','','trim');
                $fid = Request::instance()->Post('fid','','intval');
    			if (empty($name)) {
    				$this->error('修改失败');die;
    			}
    			$data = array(
    				'name' => $name,
                    'fid' => $fid
    			);
    			$is = Db::name('region')->where('id',$id)->update($data);
    			if ($is) {
    				$this->success('修改成功', Url::build('Region/pro_list'));
    			}else{
    				$this->error('更新失败');die;
    			}
    		}else{
                $flist = Db::name('region')->where('fid',0)->select();
    			$row = Db::name('region')->where('id',$id)->find();
                $this->assign('flist',$flist);
    			$this->assign('row',$row);
    			return $this->fetch();
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }

    public function pro_del(){
    	$id = Request::instance()->param('id','','intval');
    	if (!empty($id) && $id > 0) {
    		$is = Db::name('region')->where('id',$id)->delete();
    		if ($is == 0) {
    			$this->error('删除失败');die;
    		}else{
    			$this->success('删除成功', Url::build('Region/pro_list'));
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }

    
}
