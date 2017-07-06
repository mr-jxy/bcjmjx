<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\user;
use think\Request;
use think\Db;
use think\Url;
class Delegate extends Common
{
    public function n_list()
    {
        $user = new user;
        $data = 'r.id,r.order_sn,r.mobile,rl.username';
        $where = array(
            'r.repair' => '0',
            'r.type' => '0'
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
                $cid = Request::instance()->Post('cid','','intval');
                if ($cid == 0) {
                    $this->error('报修人必须选择');
                }
                $data = array(
                    'repair' => $cid,
                    'type' => 1
                );
                $is = Db::name('repair_order')->where('id',$id)->update($data);
                if ($is) {
                    $this->success('修改成功', Url::build('Delegate/n_list'));
                }else{
                    $this->error('更新失败');die;
                }
            }else{
                $row = Db::name('repair_list')->where('id',$id)->find();
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
        $user = new user;
        $data = 'r.id,r.order_sn,r.mobile,r.repair,r.type,rl.username,rl.rid';
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
                $cid = Request::instance()->Post('cid','','intval');
                if ($cid == 0) {
                    $this->error('报修人必须选择');
                }
                $data = array(
                    'repair' => $cid,
                    'type' => 1
                );
                $is = Db::name('repair_order')->where('id',$id)->update($data);
                if ($is) {
                    $this->success('修改成功', Url::build('Delegate/y_list'));
                }else{
                    $this->error('更新失败');die;
                }
            }else{
                $row = Db::name('repair_list')->where('id',$id)->find();
                $orow = Db::name('repair_order')->field('repair')->where('id',$id)->find();
                $repair = Db::name('repair')->where('rid',$row['rid'])->select();
                $rlist = Db::name('region')->where('id',$row['rid'])->find();
                $this->assign('row',$row);
                $this->assign('orow',$orow);
                $this->assign('repair',$repair);
                $this->assign('rlist',$rlist);
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
