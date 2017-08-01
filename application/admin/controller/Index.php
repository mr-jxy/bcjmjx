<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Session;
class Index extends Common
{
    public function index()
    {
        $id = Session::get('admin.id');
        if ($id > 1) {
            $rid = Session::get('admin.rid');
            $menu = Session::get('menu');
            /*$region = Db::name('region')->field('id')->where('fid','in',"$rid")->select();
            $rids = '';
            foreach ($region as $key => $value) {
                $rids.=$value['id'].',';
            }
            $rids = substr($rids,0,-1);*/
            $where = array(
                'ro.type' => 0,
                'rl.rid' => array(
                    '0' => 'in',
                    '1' => $rid
                    //'1' => $rids
                )
            );
            $ncount = Db::name('repair_order ro')->join('repair_list rl','ro.id = rl.id','left')->field('count(*) as count')->where($where)->find();
            $this->assign('menu',$menu);
        }else{
            $ncount = Db::name('repair_order')->field('count(*) as count')->where('type','0')->find();
        }
        
    	$this->assign('ncount',$ncount['count']);
        return $this->fetch();
    }

    public function main()
    {
        $id = Session::get('admin.id');
        if ($id > 1) {
            $rid = Session::get('admin.rid');
            /*$region = Db::name('region')->field('id')->where('fid','in',"$rid")->select();
            $rids = '';
            foreach ($region as $key => $value) {
                $rids.=$value['id'].',';
            }
            $rids = substr($rids,0,-1);*/
            
            $count1 = Db::name('repair_order ro')->join('repair_list rl','ro.id = rl.id','left')->field('count(*) as count')->where(array('type'=>'1','rl.rid' => array('0' => 'in','1' => $rid)))->find();
            $count2 = Db::name('repair_order ro')->join('repair_list rl','ro.id = rl.id','left')->field('count(*) as count')->where(array('type'=>'2','rl.rid' => array('0' => 'in','1' => $rid)))->find();
            $count3 = Db::name('repair_order ro')->join('repair_list rl','ro.id = rl.id','left')->field('count(*) as count')->where(array('type'=>'3','rl.rid' => array('0' => 'in','1' => $rid)))->find();
            $count4 = Db::name('repair_order ro')->join('repair_list rl','ro.id = rl.id','left')->field('count(*) as count')->where(array('type'=>'4','rl.rid' => array('0' => 'in','1' => $rid)))->find();
        }else{
            $count1 = Db::name('repair_order')->field('count(*) as count')->where(array('type'=>'1'))->find();
            $count2 = Db::name('repair_order')->field('count(*) as count')->where(array('type'=>'2'))->find();
            $count3 = Db::name('repair_order')->field('count(*) as count')->where(array('type'=>'3'))->find();
            $count4 = Db::name('repair_order')->field('count(*) as count')->where(array('type'=>'4'))->find();
        }
   	
    	$this->assign('count1',$count1['count']);
    	$this->assign('count2',$count2['count']);
    	$this->assign('count3',$count3['count']);
    	$this->assign('count4',$count4['count']);
    	return $this->fetch();
    }
}
