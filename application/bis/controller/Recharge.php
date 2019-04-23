<?php
namespace app\bis\controller;
use think\Controller;
use think\Exception;
use think\Validate;
use think\Db;

class Recharge extends Base {

    const PAGE_SIZE = 20;
    const WXPAY = 1;
    const ALIPAY = 2;
    const BIS_CATERING_TYPE = 2;

    //充值记录
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Recharge')->getRechargeCount($bis_id,self::BIS_CATERING_TYPE);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Recharge')->getRecharge($bis_id,$limit,$offset,self::BIS_CATERING_TYPE);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
        ]);
    }

    //充值页面
    public function add(){
        return $this->fetch('');
    }

    //充值，微信支付
    public function rechargeByWxpay(){
        $bis_id = session('bis_id','','bis');
        $amount = input('post.amount','');
        try{
            $res = model('Recharge')->add($bis_id,$amount,self::WXPAY,self::BIS_CATERING_TYPE);
            if(!empty($res)){
                $recharge_id = $res['recharge_id'];
                $body = $res['body'];
                return $this->redirect('http://payjulian.dxshuju.com/native.php?recharge_id='.$recharge_id.'&body='.$body.'&bis_type='.self::BIS_CATERING_TYPE);
            }else{
                $this->error('充值失败');
            }
        }catch(Exception $e){
            $this->error($e->getMessage());
        }
    }

    //充值，支付宝支付
    public function rechargeByAlipay(){
        $bis_id = session('bis_id','','bis');
        $amount = input('get.amount','');
        try{
            $res = model('Recharge')->add($bis_id,$amount,self::ALIPAY,self::BIS_CATERING_TYPE);
            if(!empty($res)){
                $recharge_id = $res['recharge_id'];
                $body = $res['body'];
                return $this->redirect('alipay/alipay?recharge_id='.$recharge_id.'&body='.$body);
            }else{
                $this->error('充值失败');
            }
        }catch(Exception $e){
            $this->error($e->getMessage());
        }
    }

    //小程序内余额充值/消费明细
    public function balance_detail(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        $res = Model('Recharge')->getBalanceDetail($bis_id,$limit,$offset);
        $count = Model('Recharge')->getBalanceCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  => $count,
        ]);

    }

}
