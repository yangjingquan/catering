<?php
namespace app\common\model;
use think\Model;
use think\Db;
class City extends Model{

    //根据父id获取市级信息
    public function getCitysByParentId($parent_id){
        $data = [
            'status' => 1,
            'parent_id' => $parent_id
        ];

        $order = [
            'id'  => 'asc'
        ];

        $res = Db::table('store_city')->where($data)->order($order)->select();
        return $res;
    }
}

?>