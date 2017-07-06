<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
class Equipment extends Common
{
    public function cat_list()
    {
    	$list = Db::name('equipment_category')->select();
    	$this->assign('list',$list);
        return $this->fetch();
    }

    public function cat_add(){
    	if (Request::instance()->isPost()) {
    		$name = Request::instance()->Post('name','','trim');
    		if (!empty($name)) {
    			$data = array(
    				'name' => $name
    			);
    			Db::name('equipment_category')->insert($data);
    			if (Db::name('equipment_category')->getLastInsID() > 0) {
    				$this->success('添加成功', Url::build('Equipment/cat_list'));
    			}else{
    				$this->error('添加失败');
    			}
    		}else{
    			$this->error('分类名不能为空!');
    		}
    	}else{
    		return $this->fetch();
    	}
    }

    public function cat_save(){
    	$id = Request::instance()->param('id','','intval');
    	if (!empty($id) && $id > 0) {
    		if (Request::instance()->isPost()) {
    			$name = Request::instance()->Post('name','','trim');
    			if (empty($name)) {
    				$this->error('修改失败');die;
    			}
    			$data = array(
    				'name' => $name
    			);
    			$is = Db::name('equipment_category')->where('id',$id)->update($data);
    			if ($is) {
    				$this->success('修改成功', Url::build('Equipment/cat_list'));
    			}else{
    				$this->error('更新失败');die;
    			}
    		}else{
    			$row = Db::name('equipment_category')->where('id',$id)->find();
    			$this->assign('row',$row);
    			return $this->fetch();
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }

    public function cat_del(){
    	$id = Request::instance()->param('id','','intval');
    	if (!empty($id) && $id > 0) {
    		$is = Db::name('equipment_category')->where('id',$id)->delete();
    		if ($is == 0) {
    			$this->error('删除失败');die;
    		}else{
    			$this->success('删除成功', Url::build('Equipment/cat_list'));
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }

    /*----------------------------------------分割线----------------------------------------------------------*/
    public function con_list()
    {
    	$list = Db::name('equipment')->select();
    	$cat = Db::name('equipment_category')->select();
    	foreach ($list as $key => $value) {
    		foreach ($cat as $ke => $val) {
    			if ($value['cid'] == $val['id']) {
    				$list[$key]['cname'] = $val['name'];
    				break;
    			}
    		}
    	}
    	unset($cat);
    	$this->assign('list',$list);
        return $this->fetch();
    }

    public function con_add(){
    	if (Request::instance()->isPost()) {
    		$name = Request::instance()->Post('name','','trim');
    		$cid = Request::instance()->Post('cid','','intval');
    		if (empty($cid) || $cid <= 0) {
    			$this->error('分类必须选择');die;
    		}
    		if (!empty($name)) {
    			$data = array(
    				'name' => $name,
    				'cid' => $cid
    			);
    			Db::name('equipment')->insert($data);
    			if (Db::name('equipment')->getLastInsID() > 0) {
    				$this->success('添加成功', Url::build('Equipment/con_list'));
    			}else{
    				$this->error('添加失败');
    			}
    		}else{
    			$this->error('产品名称不能为空!');
    		}
    	}else{
    		$cat = Db::name('equipment_category')->select();
    		$this->assign('cat',$cat);
    		return $this->fetch();
    	}
    }

    public function con_save(){
    	$id = Request::instance()->param('id','','intval');
    	if (!empty($id) && $id > 0) {
    		if (Request::instance()->isPost()) {
    			$name = Request::instance()->Post('name','','trim');
    			$cid = Request::instance()->Post('cid','','intval');
    			if (empty($name)) {
    				$this->error('修改失败');die;
    			}
    			if (empty($cid) || $cid <= 0) {
	    			$this->error('分类必须选择');die;
	    		}
    			$data = array(
    				'name' => $name,
    				'cid' => $cid
    			);
    			$is = Db::name('equipment')->where('id',$id)->update($data);
    			if ($is) {
    				$this->success('修改成功', Url::build('Equipment/con_list'));
    			}else{
    				$this->error('更新失败');die;
    			}
    		}else{
    			$cat = Db::name('equipment_category')->select();
    			$row = Db::name('equipment')->where('id',$id)->find();
    			$this->assign('cat',$cat);
    			$this->assign('row',$row);
    			return $this->fetch();
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }

    public function con_del(){
    	$id = Request::instance()->param('id','','intval');
    	if (!empty($id) && $id > 0) {
    		$is = Db::name('equipment')->where('id',$id)->delete();
    		if ($is == 0) {
    			$this->error('删除失败');die;
    		}else{
    			$this->success('删除成功', Url::build('Equipment/con_list'));
    		}
    	}else{
    		$this->error('参数不合法');
    	}
    }
}
