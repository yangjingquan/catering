<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Reserve extends Model{

    //获取桌位类型信息
    public function getAllInfo($bis_id,$limit,$offset){
        $res = Db::table('cy_reserve_table_info')
            ->where('bis_id = '.$bis_id.' and status = 1')
            ->order('updated_at desc')
            ->limit($offset,$limit)
            ->select();
        return $res;
    }

    //获取桌位类型数量
    public function getAllCount($bis_id){
        $res = Db::table('cy_reserve_table_info')
            ->where('bis_id = '.$bis_id.' and status = 1')
            ->count();
        return $res;
    }

    public function add($data){
        $res = Db::table('cy_reserve_table_info')->insertGetId($data);
        return $res;
    }

}

?>