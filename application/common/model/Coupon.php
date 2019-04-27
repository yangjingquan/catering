<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Coupon extends Model{

    //添加优惠券
    public function add($data){
        $data['status'] = 0;
        return Db::table('cy_coupon')->insert($data);
    }

    //获取所有优惠券信息
    public function getCoupons($bis_id,$limit,$offset){
        $data = [
            'status'  => ['neq',-1],
            'bis_id'   => $bis_id
        ];

        $order = [
            'update_time'  => 'desc'
        ];

        $result = Db::table('cy_coupon')
            ->where($data)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        $index = 0;
        foreach($result as $val){
            $result[$index]['start_time'] = substr($val['start_time'],0,10);
            $result[$index]['end_time'] = substr($val['end_time'],0,10);
            $index ++;
        }
        return $result;
    }

    //获取所有优惠券数量
    public function getCouponsCount($bis_id){
        $data = [
            'status'  => ['neq',-1],
            'bis_id'   => $bis_id
        ];

        $order = [
            'update_time'  => 'desc'
        ];

        $result = Db::table('cy_coupon')->where($data)->count();

        return $result;
    }
}

?>