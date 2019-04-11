<?php
namespace app\common\model;
use think\Model;
use think\Db;
use app\api\service\CheckService;
class Deduct extends Model{

    //创建扣款记录
    public function createDeductRecords($bis_id,$deductMoney,$balance,$bis_type){

        $data = array(
            'bis_id'  => $bis_id,
            'amount'  => $deductMoney,
            'balance'  => $balance,
            'bis_type'  => $bis_type,
            'created_at'  => date('Y-m-d H:i:s'),
        );
        $res = Db::table('store_deduct_records')->insert($data);
        return $res;
    }

    //获取充值记录
    public function getDeductInfo($bis_id,$limit,$offset){
        $where = [
            'bis_id' => $bis_id
        ];

        $order = [
            'created_at'  => 'desc'
        ];

        return Db::table('store_deduct_records')->where($where)->limit($offset,$limit)->order($order)->select();
    }

    //获取充值记录数量
    public function getDeductCount($bis_id){
        $where = [
            'bis_id' => $bis_id
        ];

        return Db::table('store_deduct_records')->where($where)->count();
    }

}

?>