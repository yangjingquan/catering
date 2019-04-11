<?php
/**
 * Created by PhpStorm.
 * User: yangjingquan
 * Date: 2019-03-22
 * Time: 15:42
 */

namespace app\bis\controller;
use think\Controller;
use think\Config;
use think\Db;

class Alipay extends Controller
{
    public function alipay()
    {
        // 引入支付核心文件
        Vendor('Alipay.AopSdk');
        Vendor("Alipay.pagepay.service.AlipayTradeService");
        Vendor("Alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder");

        // 获取支付宝配置参数
        $config = Config::get('alipayConfig');

        //获取充值参数
        $param = input('get.');
        $rechargeId = $param['recharge_id'];
        $rechargeRes = Db::table('store_recharge_records')->where("recharge_id = '$rechargeId'")->find();
        $amount = $rechargeRes['amount'];

        // 配置参数
        $res = array();
        $res['out_trade_no'] = $rechargeId;// 商户订单号        
        $res['subject']  = $param['body'];// 商品名称
        $res['total_amount'] = $amount;// 商品总价
        $res['body'] = $param['body'];// 商品描述

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $res["out_trade_no"];
        //订单名称，必填
        $subject = trim($res["subject"]);
        //付款金额，必填
        $total_amount = $res["total_amount"];
        //商品描述，可空
        $body = trim($res["body"]);
        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $aop = new \AlipayTradeService($config);

        $response = $aop->pagePay($payRequestBuilder, $config['return_url'], $config['notify_url']);
        //输出支付二维码
        var_dump($response);

    }
}