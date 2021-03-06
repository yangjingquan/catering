<?php
namespace app\bis\controller;
use think\Controller;
use think\Db;
use think\Exception;

class Orders extends Base {

    const BIS_CATERING_TYPE = 2;
    const NOT_SUPPLY_ORDER = 0;
    const IS_SUPPLY_ORDER = 1;

    //点餐订单列表
    public function dc_index(){
        //获取参数
        $bis_id = session('bis_id','','bis');
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status',-1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getDcAllOrdersCount($bis_id,$date_from, $date_to, $order_status);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getDcAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
        ]);
    }

    //点餐订单详情
    public function dc_detail(){
        //获取参数
        $id = input('get.id');
        Db::startTrans();
        try{
            //获取订单详情
            $order_info = Model('Orders')->getDcOrderInfoById($id);
            //获取订单内商品信息
            $sub_order_info = Model('Orders')->getDcProductInfoById($id);
            //更新订单为旧订单
            Model('Orders')->updateToOldOrder($id);

            Db::commit();
        }catch (Exception $e){
            Db::rollback();
        }

        return $this->fetch('',[
            'order_info'   => $order_info,
            'sub_order_info'   => $sub_order_info
        ]);
    }
    //外卖订单列表
    public function wm_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status',0,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getWmAllOrdersCount($date_from, $date_to, $order_status);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getWmAllOrders($limit, $offset, $date_from, $date_to,$order_status);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
        ]);
    }

    //外卖订单详情
    public function wm_detail(){
        //获取参数
        $id = input('get.id');
        //获取订单详情
        $order_info = Model('Orders')->getWmOrderInfoById($id);
        //获取订单内商品信息
        $sub_order_info = Model('Orders')->getWmProductInfoById($id);

        return $this->fetch('',[
            'order_info'   => $order_info,
            'sub_order_info'   => $sub_order_info
        ]);
    }

    //修改订单状态
    public function changeOrderStatus(){
        //获取参数
        $param = input('post.');

        if(empty($param['order_status'])){
            return $this->error('订单已完成');
        }
        $data = [
            'order_status' => $param['order_status'],
            'update_time'  => date('Y-m-d H:i:s')
        ];

        Db::startTrans();
        try{
            //若置状态为已完成，加积分
            if($param['order_status'] == 3 && $param['type'] == 1){
                //获取当前订单信息
                $orderInfo = Model('Orders')->getMainOrderOnly($param['order_id']);
                $openid = $orderInfo['mem_id'];
                $bisId = $orderInfo['bis_id'];
                $totalAmount = $orderInfo['total_amount'];
                $OrderNo = $orderInfo['order_no'];
                $this->addJifen($bisId,$openid,$totalAmount,$OrderNo);
            }
            $res = Db::table('cy_main_orders')->where('id = '.$param['order_id'])->update($data);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
        }

        if($param['type'] == 1){
            return $this->success('修改订单状态成功!','Orders/dc_index');
        }else{
            return $this->success('修改订单状态成功!','Orders/wm_index');
        }
    }

    //预定订单列表
    public function yd_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status',0,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getYdAllOrdersCount($date_from, $date_to, $order_status);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getYdAllOrders($limit, $offset, $date_from, $date_to,$order_status);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
        ]);
    }

    //确认预定订单
    public function confirm_yd_order(){
        //获取参数
        $param = input('get.');

        $data = [
            'order_status' => 3,
            'update_time'  => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_pre_orders')->where('id = '.$param['order_id'])->update($data);

        return $this->success('修改订单状态成功!','Orders/yd_index');
    }

    //取消预定订单
    public function cancel_yd_order(){
        //获取参数
        $param = input('get.');

        $data = [
            'order_status' => 2,
            'update_time'  => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_pre_orders')->where('id = '.$param['order_id'])->update($data);

        return $this->success('取消订单成功!','Orders/yd_index');
    }

    //商城普通订单列表
    public function mall_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page', 1, 'intval');
        $order_status = input('get.order_status', '0', 'intval');
        $order_from = input('get.order_from', 0, 'intval');

        $limit = 20;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getAllMallOrdersCount($date_from, $date_to, $order_status, $order_from,self::NOT_SUPPLY_ORDER);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getAllMallOrders($limit, $offset, $date_from, $date_to, $order_status, $order_from,self::NOT_SUPPLY_ORDER);
        return $this->fetch('', [
            'res' => $res,
            'pages' => $pages,
            'count' => $count,
            'current_page' => $current_page,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'order_status' => $order_status,
            'order_from' => $order_from,
        ]);
    }

    //商城供货订单列表
    public function mall_supply_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page', 1, 'intval');
        $order_status = input('get.order_status', '0', 'intval');
        $order_from = input('get.order_from', 0, 'intval');

        $limit = 20;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getAllMallOrdersCount($date_from, $date_to, $order_status, $order_from,self::IS_SUPPLY_ORDER);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getAllMallOrders($limit, $offset, $date_from, $date_to, $order_status, $order_from,self::IS_SUPPLY_ORDER);
        return $this->fetch('', [
            'res' => $res,
            'pages' => $pages,
            'count' => $count,
            'current_page' => $current_page,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'order_status' => $order_status,
            'order_from' => $order_from,
        ]);
    }

    //订单详情
    public function mall_detail()
    {
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id', '', 'bis');
        //获取运货方式
        $post_res = Db::table('store_post_mode')->where('status = 1')->select();
        //获取订单详情
        $order_info = Model('Orders')->getOrderInfoById($id);
        //获取订单内商品信息
        $sub_order_info = Model('Orders')->getProductInfoById($id);
        //判断物流状态
        $logistics_info = Model('Orders')->getLogisticsStatus($bis_id);

        return $this->fetch('', [
            'post_res' => $post_res,
            'order_info' => $order_info,
            'sub_order_info' => $sub_order_info,
            'logistics_info' => $logistics_info,
        ]);
    }

    //修改订单状态
    public function changeMallOrderStatus()
    {
        //获取参数
        $param = input('post.');
        $bis_id = session('bis_id', '', 'bis');

        //验证数据
        $validate = validate('Orders');
        if (!$validate->scene('status1')->check($param)) {
            $this->error($validate->getError());
        }

        //判断物流状态
        $logistics_info = Model('Orders')->getLogisticsStatus($bis_id);
        if ($param['order_status'] != '4') {
            $data = [
                'order_status' => $param['order_status'],
                'update_time' => date('Y-m-d H:i:s')
            ];
        } else {
            if ($logistics_info == 1) {
                //验证数据
                $validate = validate('Orders');
                if (!$validate->scene('status2')->check($param)) {
                    $this->error($validate->getError());
                }

                $data = [
                    'order_status' => $param['order_status'],
                    'mode' => $param['post_mode'],
                    'express_no' => $param['express_no'],
                    'update_time' => date('Y-m-d H:i:s')
                ];
            } else {
                //验证数据
                $validate = validate('Orders');
                if (!$validate->scene('status3')->check($param)) {
                    $this->error($validate->getError());
                }
                $data = [
                    'order_status' => $param['order_status'],
                    'update_time' => date('Y-m-d H:i:s')
                ];
            }

        }

        $res = Db::table('cy_mall_main_orders')->where('id = ' . $param['order_id'])->update($data);

        return $this->success('修改订单状态成功!');
    }

    //会员增加积分
    public function addJifen($bisId,$openid,$total_amount,$orderNo){
        //获取积分比例
        $bisInfo = Db::table('cy_bis')->where('id = '.$bisId)->find();
        $jifen_ratio = $bisInfo['jifen_ratio'];
        if($jifen_ratio > 0){
            //可获得积分
            $jifen = floor($total_amount / $jifen_ratio);
            if($jifen > 0){
                //更新会员积分
                Db::table('cy_members')->where("mem_id = '$openid'")->setInc('jifen',$jifen);
                //生成积分明细
                $this->createJifenDetail($openid,$jifen,$orderNo);
            }
        }

        return true;
    }

    //生成积分明细
    public function createJifenDetail($openid,$jifen,$order_no){
        //生成积分明细记录
        $jf_data = [
            'mem_id'  => $openid,
            'changed_jifen'  => $jifen,
            'type'  => 1,
            'remark'  => $order_no,
            'create_time'  => date('Y-m-d H:i:s'),
        ];
        Db::table('cy_jifen_detailed')->insert($jf_data);
    }

    public function getNewOrdersCount(){
        $bis_id = input('post.bis_id');
        $res = Db::table('cy_main_orders')->where('is_new = 1 and status = 1 and bis_id = '.$bis_id)->count();
        return show(1,'success',$res);
    }
}
