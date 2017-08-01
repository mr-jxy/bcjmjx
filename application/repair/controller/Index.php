<?php
namespace app\repair\controller;
use think\Db;
use think\Controller;
use think\Session;
class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }

    public function main()
    {
    	$id = Session::get('repair.id');
    	$count1 = Db::name('repair_order')->field('count(*) as count')->where(array('repair'=>$id,'type'=>'1'))->find();
    	$count2 = Db::name('repair_order')->field('count(*) as count')->where(array('repair'=>$id,'type'=>'2'))->find();
    	$count3 = Db::name('repair_order')->field('count(*) as count')->where(array('repair'=>$id,'type'=>'3'))->find();
    	$count4 = Db::name('repair_order')->field('count(*) as count')->where(array('repair'=>$id,'type'=>'4'))->find();
    	$this->assign('count1',$count1['count']);
    	$this->assign('count2',$count2['count']);
    	$this->assign('count3',$count3['count']);
    	$this->assign('count4',$count4['count']);
    	return $this->fetch();
    }
}
