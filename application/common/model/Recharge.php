<?php
namespace app\common\model;
use think\Exception;
use think\Model;
use think\Db;
use app\api\service\CheckService;
class Recharge extends Model{

    //充值
    public function add($bis_id,$amount,$pay_type,$bis_type){
        CheckService::checkEmpty($amount,'充值金额');
        $recharge_id = 're'.date('Y').date('m').date('d').date('H').date('i').date('s').rand(100000,999999);
        $body = '充值 '.$amount.' 元';
        $payData = array();

        Db::startTrans();
        try{
            $bisInfo = $this->getBisInfo($bis_id);
            if(empty($bisInfo['balance'])){
                $balance = '0.00';
            }else{
                $balance = $bisInfo['balance'];
            }

            $data = array(
                'bis_id'  => $bis_id,
                'amount'  => $amount,
                'balance'  => $amount + $balance,
                'recharge_id'  => $recharge_id,
                'pay_type'  => $pay_type,
                'bis_type'  => $bis_type,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            );
            $payData = array(
                'bis_id' => $bis_id,
                'recharge_id' => $recharge_id,
                'body' => $body,
            );
            $res = Db::table('store_recharge_records')->insert($data);
            $updateBisData['balance'] = $amount + $balance;
            //更新店铺充值金额信息
            $bisRes = Db::table('cy_bis')->where('id = '.$bis_id)->update($updateBisData);

            Db::commit();
        }catch (Exception $e){
            Db::rollback();
        }

        return $payData;
    }

    //获取充值记录
    public function getRecharge($bis_id,$limit,$offset,$bis_type){
        $where = [
            'bis_id' => $bis_id,
            'recharge_status'  => 2,
            'bis_type'  => $bis_type
        ];

        $order = [
            'created_at'  => 'desc'
        ];

        return Db::table('store_recharge_records')->where($where)->limit($offset,$limit)->order($order)->select();
    }

    //获取充值记录数量
    public function getRechargeCount($bis_id,$bis_type){
        $where = [
            'bis_id' => $bis_id,
            'recharge_status'  => 2,
            'bis_type'  => $bis_type
        ];

        return Db::table('store_recharge_records')->where($where)->count();
    }

    //获取当前店铺信息
    public function getBisInfo($bis_id){
        $res = Db::table('cy_bis')->where('id = '.$bis_id)->find();
        if(empty($res)){
            return array();
        }else{
            return $res;
        }
    }

    //更新当前店铺余额
    public function updateBalance($id,$balance){
        $updateData = array(
            'balance'  => $balance,
            'updated_at'  => date('Y-m-d H:i:s'),
        );
        $res = Db::table('store_recharge_records')->where('id = '.$id)->update($updateData);

        return $res;
    }
}

?>