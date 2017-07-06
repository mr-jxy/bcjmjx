<?
namespace app\Common;
use think\Controller;
use think\Session;
/**
* 后台公共类
*/
class Common extends Controller
{
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	* 无级分类
	*/
	public function tree(&$data,$pid=0,$count=0){
		if (!isset($data['old'])) {
			$data = array('new' => array(),'old' => $data);
		}
		foreach ($data['old'] as $key => $value) {
			if ($value['fid'] == $pid) {
				$value['count'] = $count;
				$data['new'][] = $value;
				unset($data['old'][$key]);
				$this->tree($data,$value['id'],$count+1);
			}
		}
		return $data['new'];
	}
}
?>