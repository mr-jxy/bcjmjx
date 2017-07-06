<?
namespace app\repair\controller;
use think\Controller;
use think\Session;
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
		if (!Session::has('repair')) {
			$this->redirect('Login/index');
		}
	}
}
?>