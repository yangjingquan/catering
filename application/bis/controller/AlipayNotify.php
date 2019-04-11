<?php
/**
 * Created by PhpStorm.
 * User: yangjingquan
 * Date: 2019-03-22
 * Time: 15:42
 */

namespace app\bis\controller;
use think\Controller;
use think\Db;
use think\Config;

class AlipayNotify extends Controller
{

    public function ailpay_notify()
    {
        // 引入核心类文件
        Vendor("Alipay.pagepay.service.AlipayTradeService");
        // 获取支付宝配置参数
        $config = Config::get('alipayConfig');
        // 获取返回值
        $arr = $_POST;
        // 检查数据
        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST, true));
        $result = $alipaySevice->check($arr);
        // 判断检查结果数据
        if ($result) {
            // 获取相关数据
            $out_trade_no = $arr['out_trade_no'];       //商户订单号
            $trade_no = $arr['trade_no'];           //支付宝交易号
            $trade_status = $arr['trade_status'];       //交易状态
            $total_amount = $arr['total_amount'];       //交易金额
            // 判断数据是否做过处理，如果做过处理，return，没有做过处理，执行支付成功代码
            if ($trade_status == 'TRADE_FINISHED' OR $trade_status == 'TRADE_SUCCESS') {
                $updateData = array(
                    'transaction_id'  =>  $trade_no,
                    'updated_at'  =>  date('Y:m-d H:i:s'),
                    'recharge_status'  =>  2
                );
                Db::table('store_recharge_records')->where("recharge_id = '$out_trade_no'")->update($updateData);
//                echo "success";
            } else {
//                echo "fail";
            }

        } else {
            echo "fail";
        }
    }
}