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
            //图片处理
            $images = Request::instance()->Post('images/a');
            if (!empty($images)) {
                $images = implode(',', $images);
            }else{
                $images = '';
            }


    		$rid = Request::instance()->Post('rid','','intval');
            if ($rid == 0) {
                $this->error('地区必须选择');
            }

            /*$zid = Request::instance()->Post('zid','','intval');
            if ($zid == 0) {
                $this->error('市区必须选择');
            }*/

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

            $eq_id = Request::instance()->Post('eq_id','','intval');
            $eq_info = Request::instance()->Post('eq_info','','trim');
            if ($eq_id == 0 && empty($eq_info)) {
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
				//'rid' => $zid,
                'rid' => $rid,
                'address' => $address,
				'username' => $username,
				'contacts' => $contacts,
				'phone' => $phone,
				'email' => $email,
				'cid' => $cid,
				'eq_id' => $eq_id,
                'eq_info' => $eq_info,
				'eq_num' => $eq_num,
				'factory_date' => $factory_date,
				'info' => $info,
                'image' => $images
			);
			$is = Db::name('repair_list')->insert($data_list);
			if ($is == 1) {
                if (isPhone($phone)) {
                    zendSms($phone,'【北村精密机械】您的订单提交成功,我们会尽快为您处理');                        
                }
                $admin_user = Db::query('select id,name,email from bc_admin_user where find_in_set('.$rid.',rid)');
                if ($admin_user) {
                    foreach ($admin_user as $key => $value) {
                        if (!empty($value['email'])) {
                            zendEmail($value['email'],'您有一个新的报修订单需要处理，联系人：'.$contacts.'， 联系电话：'.$phone.'请及时登录系统进行处理。');
                        }
                    }
                }
				$this->success('添加成功', Url::build('Index/prompt',array('order_sn'=>$order_sn)));
			}else{
				$this->error('添加失败');
			}
    	}else{
    		$cat = Db::name('equipment_category')->select();
    		$rlist = Db::name('region')->where('fid','<>','0')->select();
            $this->assign('cat', $cat);
            $this->assign('rlist', $rlist);
    		return $this->fetch();
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

    public function ld(){
        $cid = Request::instance()->param('cid','','trim');
        $list = Db::name('equipment')->where(array('cid' => $cid))->select();
        echo json_encode($list);
    }

    public function rld(){
        $fid = Request::instance()->param('fid','','trim');
        $list = Db::name('region')->where(array('fid' => $fid))->select();
        echo json_encode($list);
    }

    public function prompt(){
        $order_sn = Request::instance()->param('order_sn','','trim');
        $this->assign('order_sn', $order_sn);
        return $this->fetch();
    }
}
