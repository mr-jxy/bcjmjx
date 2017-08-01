<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/**
* 
*/
class User extends Model
{
	public static function userinfo($data,$where,$order = 'id desc'){
		return Db::name('repair')->field($data)->where($where)->order($order)->select();
	}

	public static function n_list($data,$where,$order = 'r.id desc'){
		return Db::name('repair_order')->alias('r')->join('bc_repair_list rl','r.id = rl.id','LEFT')->field($data)->where($where)->order($order)->select();
	}
}
?>