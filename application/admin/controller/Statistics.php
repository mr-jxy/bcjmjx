<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\user;
use think\Request;
use think\Db;
use think\Url;
use think\Session;
class Statistics extends Common
{
    public function repair()
    {
    	$list = Db::name('repair_order')->field('mobile,count(*) as count,addtime')->group('mobile')->having('count>0')->order('count desc')->select();
    	$this->assign('list',$list);
        return $this->fetch();
    }

    function repair_export()
    {
        if (Request::instance()->isPost()) {
            $starttime = strtotime(Request::instance()->Post('starttime','','trim'));
            $endtime = strtotime(Request::instance()->Post('endtime','','trim'));
            if ($endtime < $starttime) {
                $this->error('结束时间不能小于开始时间');
            }
            $list = Db::name('repair_order')->field('id,mobile,count(*) as count,addtime')->where('addtime between "$starttime" and "$endtime"')->group('mobile')->having('count>0')->order('count desc')->select();

            $xlsName  = 'repair_export';
            $xlsrow = array(
                array('id','编号'),
                array('mobile','电话号'),
                array('count','次数')
            );
            exportExcel($xlsName,$xlsrow,$list);
        }
    }

    public function equipment()
    {
    	$list = Db::name('repair_list')->field('eq_id,count(*) as count')->group('eq_id')->having('count>0')->order('count desc')->select();
    	$this->assign('list',$list);
        return $this->fetch();
    }

    public function region()
    {
    	$list = Db::name('repair_list rl')->join('region reg','rl.rid = reg.id')->field('rl.rid,count(*) as count,reg.name as regname')->group('rl.rid')->having('count>0')->order('count desc')->select();
    	$this->assign('list',$list);
        return $this->fetch();
    }
}
