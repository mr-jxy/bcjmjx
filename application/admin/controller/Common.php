<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;
use think\Url;
use think\Request;
/**
* 后台公共类
*/
class Common extends controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->checkLogin();
	}

	/**
	* 验证登录
	*/
	function checkLogin(){
		/*验证是否登录*/
		if (!Session::has('admin')) {
			$this->redirect('Login/index');
		}else{
			$id = Session::get('admin.id');
			if ($id > 1) {
				$request = Request::instance();
				$row = Db::name('admin_user au')->join('role ro','au.role = ro.id','left')->field('au.name,au.role,ro.rules')->where('au.id', $id)->find();
				$irules = explode(',', $row['rules']);
				$rules = Db::name('rule')->field('id,title,name,fid')->select();
				$menu = '';
				foreach ($irules as $key => $value) {
					foreach ($rules as $ke => $val) {
						if ($value == $val['id']) {
							$irules[$key] = 'admin/'.$val['name'];
							$menu.= $val['title'].',';
						}
					}
				}
				Session::set('menu',$menu);
				//echo $menu;die;
				unset($rules);
				$irules = implode(',',$irules);
				$irules.= ',admin/login/index,admin/login/out,admin/index/index,admin/index/main';
				
				if (strrpos($request->path(),'/id')) {
					$news = substr($request->path(),0,strrpos($request->path(),'/id'));     //新的$a值
				}elseif(strrpos($request->path(),'/type')){
					$news = substr($request->path(),0,strrpos($request->path(),'/type'));     //新的$a值
				}else{
					$news = $request->path();
				}

				if (strpos($irules, $news) === false) {
					$this->success('权限不足哦', Url::build('Index/main'));
				}
			}
		}
	}
}
?>