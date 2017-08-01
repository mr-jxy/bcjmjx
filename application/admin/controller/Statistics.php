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
    	$list = Db::name('repair_order ro')->join('repair_list rl','ro.id = rl.id','left')->field('ro.id,ro.username_note,ro.mobile,count(*) as count,ro.addtime,rl.username')->group("IF(ro.username_note <> '',ro.username_note,rl.username)")->having('count>0')->order('count desc')->select();
        $n_str = '';
        $c_str = '';
        foreach ($list as $key => $value) {
            if (empty($value['username_note'])) {
                $list[$key]['username_note'] = $value['username'];
            }
            if ($key<6) {
                $n_str.= '"'.$list[$key]['username_note'].'",';
                $c_str.= $value['count'].',';
            }           
        }
        $n_str = substr($n_str, 0,-1);
        $c_str = substr($c_str, 0,-1);

        $this->assign('n_str',$n_str);
        $this->assign('c_str',$c_str);
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
            $list = Db::name('repair_order ro')->join('repair_list rl','ro.id = rl.id','left')->field('ro.id,ro.username_note,ro.mobile,count(*) as count,ro.addtime,rl.username')->group("IF(ro.username_note <> '',ro.username_note,rl.username)")->having('count>0')->order('count desc')->select();

            foreach ($list as $key => $value) {
                if (empty($value['username_note'])) {
                    $list[$key]['username_note'] = $value['username'];
                }
            }

            $xlsName  = 'repair_export';
            $xlsrow = array(
                array('id','编号'),
                array('username_note','公司名'),
                array('count','次数')
            );
            exportExcel($xlsName,$xlsrow,$list);
        }
    }

    public function equipment()
    {
    	$list = Db::name('repair_list rl')->join('equipment e','rl.eq_id = e.id','left')->field('count(*) as count,rl.eq_id,e.name as ename')->group('rl.eq_id')->having('count>0')->order('count desc')->select();
        $n_str = '';
        $c_str = '';
        foreach ($list as $key => $value) {
            $n_str.= '"'.$value['ename'].'",';
            $c_str.= $value['count'].',';
        }
        $n_str = substr($n_str, 0,-1);
        $c_str = substr($c_str, 0,-1);
        $this->assign('n_str',$n_str);
        $this->assign('c_str',$c_str);
    	$this->assign('list',$list);
        return $this->fetch();
    }

    function equipment_export()
    {
        if (Request::instance()->isPost()) {
            $starttime = strtotime(Request::instance()->Post('starttime','','trim'));
            $endtime = strtotime(Request::instance()->Post('endtime','','trim'));
            if ($endtime < $starttime) {
                $this->error('结束时间不能小于开始时间');
            }
            $where = array(
                'ro.addtime' =>array(
                    '0' => '>',
                    '1' => $starttime
                ),
                'ro.endtime' =>array(
                    '0' => '<',
                    '1' => $endtime
                )
            );
            $list = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id','left')->join('equipment e','rl.eq_id = e.id','left')->field('count(*) as count,rl.id,rl.eq_id,e.name as ename')->where($where)->group('rl.eq_id')->having('count>0')->order('count desc')->select();

            $xlsName  = 'requipment_export';
            $xlsrow = array(
                array('id','编号'),
                array('ename','设备列表'),
                array('count','次数')
            );
            exportExcel($xlsName,$xlsrow,$list);
        }
    }

    public function region()
    {
    	$list = Db::name('repair_list rl')->join('region reg','rl.rid = reg.id','left')->field('rl.rid,count(*) as count,reg.name as regname')->group('rl.rid')->having('count>0')->order('count desc')->select();
    	$this->assign('list',$list);
        return $this->fetch();
    }

    function region_export()
    {
        if (Request::instance()->isPost()) {
            $starttime = strtotime(Request::instance()->Post('starttime','','trim'));
            $endtime = strtotime(Request::instance()->Post('endtime','','trim'));
            if ($endtime < $starttime) {
                $this->error('结束时间不能小于开始时间');
            }
            $where = array(
                'ro.addtime' =>array(
                    '0' => '>',
                    '1' => $starttime
                ),
                'ro.endtime' =>array(
                    '0' => '<',
                    '1' => $endtime
                )
            );
            $list = Db::name('repair_list rl')->join('repair_order ro','rl.id = ro.id','left')->join('region reg','rl.rid = reg.id','left')->field('rl.rid,count(*) as count,rl.id,reg.name as regname')->where($where)->group('rl.rid')->having('count>0')->order('count desc')->select();

            $xlsName  = 'region_export';
            $xlsrow = array(
                array('id','编号'),
                array('regname','地区名称'),
                array('count','次数')
            );
            exportExcel($xlsName,$xlsrow,$list);
        }
    }


    public function staff()
    {
        $list = Db::name('repair_order ro')->join('repair r','ro.repair = r.id','left')->field('ro.id,count(*) as count,r.username')->where('ro.repair','<>','0')->group("ro.repair")->having('count>0')->order('count desc')->select();
        $n_str = '';
        $c_str = '';
        foreach ($list as $key => $value) {
            if ($key<6) {
                $n_str.= '"'.$list[$key]['username'].'",';
                $c_str.= $value['count'].',';
            }           
        }
        $n_str = substr($n_str, 0,-1);
        $c_str = substr($c_str, 0,-1);

        $this->assign('n_str',$n_str);
        $this->assign('c_str',$c_str);
        $this->assign('list',$list);
        return $this->fetch();
    }

    function staff_export()
    {
        if (Request::instance()->isPost()) {
            $starttime = strtotime(Request::instance()->Post('starttime','','trim'));
            $endtime = strtotime(Request::instance()->Post('endtime','','trim'));
            if ($endtime < $starttime) {
                $this->error('结束时间不能小于开始时间');
            }
            $where = array(
                'ro.addtime' =>array(
                    '0' => '>',
                    '1' => $starttime
                ),
                'ro.endtime' =>array(
                    '0' => '<',
                    '1' => $endtime
                ),
                'ro.repair' => array(
                    '0' => '<>',
                    '1' => '0'
                )
            );
            $list = Db::name('repair_order ro')->join('repair r','ro.repair = r.id','left')->field('ro.id,count(*) as count,r.username')->where($where)->group("ro.repair")->having('count>0')->order('count desc')->select();

            $xlsName  = 'staff_export';
            $xlsrow = array(
                array('id','编号'),
                array('username','维修人名称'),
                array('count','次数')
            );
            exportExcel($xlsName,$xlsrow,$list);
        }
    }
}
