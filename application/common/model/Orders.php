<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Orders extends Model{

    //获取点餐所有订单信息
    public function getDcAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status){
        $where = "mo.type = 1 and mo.bis_id = ".$bis_id." and  mo.status = 1";

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status != -1){
            $where .= " and mo.order_status = '$order_status'";
        }
        $listorder = [
            'mo.create_time'  => 'desc'
        ];

        $res = Db::table('cy_main_orders')->alias('mo')->field('mo.id as order_id,mo.table_id,mo.order_no,mo.order_status,mo.total_amount,mo.remark,mo.create_time,mo.is_new,mo.bis_id')
            ->join('cy_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();
        return $res;
    }

    //获取点餐所有订单数量
    public function getDcAllOrdersCount($bis_id,$date_from,$date_to,$order_status){
        $where = "mo.type = 1 and mo.bis_id = ".$bis_id." and  mo.status = 1";

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status != -1){
            $where .= " and mo.order_status = '$order_status'";
        }

        $res = Db::table('cy_main_orders')->alias('mo')
            ->join('cy_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->where($where)
            ->count();
        return $res;
    }

    //获取外卖所有订单信息
    public function getWmAllOrders($limit, $offset, $date_from, $date_to,$order_status){
        $bis_id = session('bis_id','','bis');
        $where = "mo.type = 2 and mo.bis_id = ".$bis_id." and  mo.status = 1";

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }
        $listorder = [
            'mo.create_time'  => 'desc'
        ];

        $res = Db::table('cy_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.order_status,mo.total_amount,mo.create_time,mo.rec_name,mo.mobile,mo.address')
            ->join('cy_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();
        return $res;
    }

    //获取外卖所有订单数量
    public function getWmAllOrdersCount($date_from,$date_to,$order_status){
        $bis_id = session('bis_id','','bis');
        $where = "mo.type = 2 and mo.bis_id = ".$bis_id." and  mo.status = 1";

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }

        $res = Db::table('cy_main_orders')->alias('mo')
            ->join('cy_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->where($where)
            ->count();
        return $res;
    }
    //根据id获取点餐订单详情
    public function getDcOrderInfoById($id){
        //设置查询条件
        $where = [
            'mo.id'  => $id
        ];

        //查询结果
        $res = Db::table('cy_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.remark,mo.total_amount,SUM(sub.amount) as amount,sub.pro_id,mo.order_status,mo.table_id,mo.create_time')
            ->join('cy_sub_orders sub','sub.main_id = mo.id','LEFT')
            ->join('cy_members mem','mo.mem_id = mem.id','LEFT')
            ->where($where)
            ->find();
        return $res;
    }

    //根据订单id获取点餐订单下商品信息
    public function getDcProductInfoById($id){
        //设置查询条件
        $where = [
            'so.main_id'  => $id,
            'so.status' => 1
        ];

        $order = [
            'so.id'   => 'asc'
        ];

        //查询结果
        $res = Db::table('cy_sub_orders')->alias('so')->field('so.pro_id,p.p_name,so.count,so.unit_price,so.amount')
                ->join('cy_products p','so.pro_id = p.id','LEFT')
                ->where($where)
                ->order($order)
                ->select();
        return $res;
    }
    //根据id获取外卖订单详情
    public function getWmOrderInfoById($id){
        //设置查询条件
        $where = [
            'mo.id'  => $id
        ];

        //查询结果
        $res = Db::table('cy_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.remark,mo.total_amount,SUM(sub.amount) as amount,mo.order_status,mo.create_time,mo.rec_name,mo.mobile,mo.address')
            ->join('cy_sub_orders sub','sub.main_id = mo.id','LEFT')
            ->join('cy_members mem','mo.mem_id = mem.id','LEFT')
            ->where($where)
            ->find();
        return $res;
    }

    //根据订单id获取外卖订单下商品信息
    public function getWmProductInfoById($id){
        //设置查询条件
        $where = [
            'so.main_id'  => $id,
            'so.status' => 1
        ];

        $order = [
            'so.id'   => 'asc'
        ];

        //查询结果
        $res = Db::table('cy_sub_orders')->alias('so')->field('so.pro_id,p.p_name,so.count,so.unit_price,so.amount')
            ->join('cy_products p','so.pro_id = p.id','LEFT')
            ->where($where)
            ->order($order)
            ->select();
        return $res;
    }

    //获取预定订单信息
    public function getYdAllOrders($limit, $offset, $date_from, $date_to,$order_status){
        $bis_id = session('bis_id','','bis');

        $where = "pre.bis_id = ".$bis_id." and  pre.status = 1";

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and pre.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and pre.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and pre.order_status = '$order_status'";
        }
        $listorder = [
            'pre.create_time'  => 'desc'
        ];

        $res = Db::table('cy_pre_orders')->alias('pre')->field('pre.id as order_id,pre.order_status,pre.date,pre.time,pre.remark,pre.create_time,pre.type,pre.link_man,pre.count,pre.mobile')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();

        $index = 0;
        foreach($res as $val){
            switch ($val['type']){
                case 0 :
                    $res[$index]['type'] = '二十人桌';
                    break;
                case 1 :
                    $res[$index]['type'] = '十六人桌';
                    break;
                case 2 :
                    $res[$index]['type'] = '十二人桌';
                    break;
                case 3 :
                    $res[$index]['type'] = '十人桌';
                    break;
                case 4 :
                    $res[$index]['type'] = '四人桌';
                    break;
                default :
                    $res[$index]['type'] = '二人桌';
                    break;
            }
            $index ++;
        }
        return $res;
    }

    //获取预定订单数量
    public function getYdAllOrdersCount($date_from,$date_to,$order_status){
        $bis_id = session('bis_id','','bis');
        $where = "pre.bis_id = ".$bis_id." and  pre.status = 1";

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and pre.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and pre.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and pre.order_status = '$order_status'";
        }

        $res = Db::table('cy_pre_orders')->alias('pre')
            ->where($where)
            ->count();
        return $res;
    }

    //获取所有订单信息
    public function getAllMallOrders($limit, $offset, $date_from, $date_to, $order_status, $order_from,$supply_order_status)
    {
        $bis_id = session('bis_id', '', 'bis');
        $where = "mo.bis_id = " . $bis_id . " and mem.bis_id = " . $bis_id . " and  mo.status = 1 and pay.status = 1 and mo.is_supply_order = ".$supply_order_status;

        if ($order_from != 0) {
            $where .= " and mo.order_from = $order_from ";
        }

        if ($date_from) {
            $new_date_from = $date_from . ' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if ($date_to) {
            $new_date_to = $date_to . ' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if ($order_status) {
            $where .= " and mo.order_status = '$order_status'";
        }
        $listorder = [
            'mo.create_time' => 'desc'
        ];

        $res = Db::table('cy_mall_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mode.post_mode,mem.nickname,mo.rec_name,pay.payment,mo.express_no,mo.order_status,mo.order_from,mo.order_type,mo.jifen')
            ->join('store_payment pay', 'mo.payment = pay.id', 'LEFT')
            ->join('store_post_mode mode', 'mo.mode = mode.id', 'LEFT')
            ->join('cy_members mem', 'mo.mem_id = mem.mem_id', 'LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();

        return $res;
    }

    //获取所有订单数量
    public function getAllMallOrdersCount($date_from, $date_to, $order_status, $order_from,$supply_order_status)
    {
        $bis_id = session('bis_id', '', 'bis');
        $where = "mo.bis_id = " . $bis_id . " and mem.bis_id = " . $bis_id . " and  mo.status = 1 and pay.status = 1 and mo.is_supply_order = ".$supply_order_status;

        if ($order_from != 0) {
            $where .= " and mo.order_from = $order_from ";
        }

        if ($date_from) {
            $where .= " and mo.create_time >= '$date_from'";
        }
        if ($date_to) {
            $where .= " and mo.create_time < '$date_to'";
        }
        if ($order_status) {
            $where .= " and mo.order_status = '$order_status'";
        }

        $res = Db::table('cy_mall_main_orders')->alias('mo')
            ->join('store_payment pay', 'mo.payment = pay.id', 'LEFT')
            ->join('store_post_mode mode', 'mo.mode = mode.id', 'LEFT')
            ->join('cy_members mem', 'mo.mem_id = mem.mem_id', 'LEFT')
            ->where($where)
            ->count();
        return $res;
    }

    //根据id获取订单详情
    public function getOrderInfoById($id)
    {
        //设置查询条件
        $where = [
            'mo.id' => $id
        ];

        //查询结果
        $res = Db::table('cy_mall_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.mode,mode.post_mode,mo.rec_name,mo.address,mo.mobile,pay.payment,mo.express_no,mo.order_status,mo.create_time,mo.delivery_cost,mo.remark,mo.total_amount,SUM(sub.amount) as amount,sub.pro_id,mo.is_supply_order')
            ->join('cy_mall_sub_orders sub', 'sub.main_id = mo.id', 'LEFT')
            ->join('store_payment pay', 'mo.payment = pay.id', 'LEFT')
            ->join('store_post_mode mode', 'mo.mode = mode.id', 'LEFT')
            ->join('cy_members mem', 'mo.mem_id = mem.id', 'LEFT')
            ->where($where)
            ->find();
        return $res;
    }

    //根据订单id获取订单下商品信息
    public function getProductInfoById($id)
    {
        //设置查询条件
        $where = [
            'so.main_id' => $id,
            'so.status' => 1
        ];

        $order = [
            'so.id' => 'asc'
        ];

        //查询结果
        $res = Db::table('cy_mall_sub_orders')->alias('so')->field('so.pro_id,p.p_name,so.count,so.unit_price,so.amount,p.id as ori_pro_id,p.supply_pro_id')
            ->join('cy_mall_pro_config con', 'so.pro_id = con.id', 'LEFT')
            ->join('cy_mall_products p', 'con.pro_id = p.id', 'LEFT')
            ->where($where)
            ->order($order)
            ->select();
        return $res;
    }

    //判断物流状态
    public function getLogisticsStatus($bis_id)
    {
        $res = Db::table('cy_bis')->field('logistics_status')->where('id = ' . $bis_id)->find();
        $logistics_status = $res['logistics_status'];
        return $logistics_status;
    }

    //根据订单id获取订单下供货商品金额
    public function getSupplyProductTotalAmount($id)
    {
        //设置查询条件
        $where = "so.main_id = '$id' and so.status = 1 and (p.supply_pro_id != '' or p.supply_pro_id != null or p.supply_pro_id != 0)";

        //查询结果
        $res = Db::table('cy_mall_sub_orders')->alias('so')->field('so.pro_id,p.p_name,so.count,so.unit_price,p.rate,so.rate_amount,so.amount,p.id as ori_pro_id,p.supply_pro_id')
            ->join('cy_mall_pro_config con', 'so.pro_id = con.id', 'LEFT')
            ->join('cy_mall_products p', 'con.pro_id = p.id', 'LEFT')
            ->where($where)
            ->sum('so.amount');
        if(empty($res)){
            return '0.00';
        }
        return $res;
    }

    //根据id获取点餐/外卖主订单信息
    public function getMainOrderOnly($orderId){
        $res = Db::table('cy_main_orders')->where('id = '.$orderId)->find();
        return $res;
    }

    //更新点餐订单为旧订单
    public function updateToOldOrder($id){
        $data['is_new'] = 0;
        Db::table('cy_main_orders')->where('id = '.$id)->update($data);
        return true;
    }
}

?>