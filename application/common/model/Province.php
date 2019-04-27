<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Province extends Model{

    //获取省级信息
    public function getProvinceInfo(){
        $order = [
            'id'  => 'asc'
        ];
        $where['status'] = 1;
        return Db::table('store_province')->where($where)->order($order)->select();
    }

    //根据父id获取市级信息
    public function getCitysById($proviceId){
        $data = [
            'status' => 1,
            'parent_id' => $proviceId
        ];

        $order = [
            'id'  => 'asc'
        ];

        $res = Db::table('store_city')->where($data)->order($order)->select();
        return $res;
    }
}

?>