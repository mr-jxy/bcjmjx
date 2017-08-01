<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/**
* 
*/
class User extends Model
{
	public static function userinfo(){
		return Db::name('repair')->alias('r')->join('bc_region rg','r.rid = rg.id','left')->field('r.*,rg.name as rname')->select();
	}

	public static function n_list($data,$where){
		return Db::name('repair_order')->alias('r')->join('bc_repair_list rl','r.id = rl.id','left')->field($data)->where($where)->select();
	}
}
?>