<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
class Index extends Controller
{
    public function index()
    {
    	if (Request::instance()->isPost()) {
    		$rid = Request::instance()->Post('rid','','intval');
            if ($rid == 0) {
                $this->error('地区必须选择');
            }else{
                $fid = Db::name('region')->field('fid')->where('id',$rid)->find();
                if ($fid['fid'] == 0) {
                    $this->error('请选择详细区域');
                }
            }

            $address = Request::instance()->Post('address','','trim');
            if (empty($address)) {
                $this->error('详细地址不能为空');
            }

    		$username = Request::instance()->Post('username','','trim');
            if (empty($username)) {
                $this->error('名称不能为空');
            }

            $contacts = Request::instance()->Post('contacts','','trim');
            if (empty($contacts)) {
                $this->error('联系人不能为空');
            }

            $phone = Request::instance()->Post('phone','','trim');
            if (empty($phone)) {
                $this->error('联系电话不能为空');
            }else{
                if (!preg_match("/^1[3|4|5|8|7][0-9]\d{4,8}$/", $phone)) {
                    $this->error('联系电话格式不正确');
                }
            }

            $email = Request::instance()->Post('email','','trim');
            if (empty($email)) {
                $this->error('email不能为空');
            }

            $cid = Request::instance()->Post('cid','','intval');
            if ($cid == 0) {
                $this->error('设备分类必须选择');
            }

            $eq_id = Request::instance()->Post('eq_id','','trim');
            if (empty($eq_id)) {
                $this->error('设备型号不能为空');
            }

            $eq_num = Request::instance()->Post('eq_num','','trim');
            if (empty($eq_num)) {
                $this->error('设备编号不能为空');
            }

            $factory_date = Request::instance()->Post('factory_date','','trim');
            if (empty($factory_date)) {
                $this->error('出厂日期不能为空');
            }

            $info = Request::instance()->Post('info','','trim');
            if (empty($info)) {
                $this->error('设备鼓掌描述不能为空');
            }

            /*生成订单号*/
            $order_sn = order_sn();
            $data = array(
            	'order_sn' => $order_sn,
            	'mobile' => $phone,
            	'type' => '0',
                'addtime' => $_SERVER['REQUEST_TIME']
            );
        	Db::name('repair_order')->insert($data);
        	$data_list = array(
        		'id' => Db::name('repair_order')->getLastInsID(),
				'rid' => $rid,
                'address' => $address,
				'username' => $username,
				'contacts' => $contacts,
				'phone' => $phone,
				'email' => $email,
				'cid' => $cid,
				'eq_id' => $eq_id,
				'eq_num' => $eq_num,
				'factory_date' => $factory_date,
				'info' => $info
			);
			$is = Db::name('repair_list')->insert($data_list);
			if ($is == 1) {
				$this->success('添加成功', Url::build());
			}else{
				$this->error('添加失败');
			}
    	}else{
    		$cat = Db::name('equipment_category')->select();
    		$rlist = Db::name('region')->select();
            $rlist = tree($rlist);
            $rlist = printTree($rlist,'|-');
            $this->assign('cat', $cat);
            $this->assign('rlist', $rlist);
    		return $this->fetch();
    	}
    }
}
