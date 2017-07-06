<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Url;
class Region extends Common
{
    public function pro_list()
    {
    	$list = Db::name('region')->select();
        $list = tree($list);
        $list = printTree($list,'|-');
    	$this->assign('list',$list);
        return $this->fetch();
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
