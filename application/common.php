<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//设置正常信息状态
function status($status)
{
    if ($status == 1) {
        $str = "<span class='label label-success radius'>正常</span>";
    } elseif ($status == 0) {
        $str = "<span style='color: red;' class='label label-success radius'>待审</span>";
    } else {
        $str = "<span class='label label-success radius'>删除</span>";
    }

    return $str;
}

//设置ajax返回
function show($status, $message, $data = [])
{
    return [
        'status' => intval($status),
        'message' => $message,
        'data' => $data
    ];
}

//设置商品上架状态
function p_status($on_sale)
{
    if ($on_sale == 1) {
        $str = "<span class='label label-success radius'>上架</span>";
    } elseif ($on_sale == 0) {
        $str = "<span style='color: red;' class='label label-success radius'>未上架</span>";
    } else {
        $str = "<span class='label label-success radius'>删除</span>";
    }

    return $str;
}

//设置点餐订单状态
function dc_order_status($status)
{
    if ($status == 0) {
        $str = "<span class='label label-success radius'>未确认</span>";
    } elseif ($status == 1) {
        $str = "<span class='label label-success radius'>已点餐</span>";
    }elseif ($status == 2) {
        $str = "<span class='label label-success radius'>已付款</span>";
    }else {
        $str = "<span style='color: red' class='label label-success radius'>完成</span>";
    }

    return $str;
}

//设置外卖订单状态
function wm_order_status($status)
{
    if ($status == 1) {
        $str = "<span class='label label-success radius'>未付款</span>";
    } elseif ($status == 2) {
        $str = "<span class='label label-success radius'>已付款</span>";
    } elseif ($status == 3) {
        $str = "<span class='label label-success radius'>配送中</span>";
    } else {
        $str = "<span class='label label-success radius'>完成</span>";
    }

    return $str;
}

//设置预定订单状态
function yd_order_status($status)
{
    if ($status == 1) {
        $str = "<span class='label label-success radius'>预定中</span>";
    } elseif ($status == 2) {
        $str = "<span class='label label-success radius'>取消</span>";
    } else {
        $str = "<span class='label label-success radius'>预定完成</span>";
    }

    return $str;
}

//确认预定订单
function confirm_yd_order_status($status)
{
    $str = "<span class='label radius' style='background-color: red'>确认</span>";

    return $str;
}

//取消预定订单
function cancel_yd_order_status($status)
{
    $str = "<span class='label radius' style='background-color: #bbbbbb'>取消</span>";

    return $str;
}

//设置点餐订单状态
function table_show($status)
{
    if ($status == 1) {
        $str = "<span class='label label-success radius'>可用</span>";
    } else {
        $str = "<span style='color: red;' class='label label-success radius'>不可用</span>";
    }
    return $str;
}

//设置活动状态
function activity_status($status)
{
    if ($status == 1) {
        $str = "<span class='label label-success radius'>上线</span>";
    } else {
        $str = "<span style='color: red;' class='label label-success radius'>下线</span>";
    }
    return $str;
}

//设置商品推荐状态
function pro_recommend($is_recommend)
{
    if ($is_recommend == 1) {
        $str = "<span style='color:red;' class='label label-success radius'>取消推荐</span>";
    } else {
        $str = "<span class='label label-success radius'>设置推荐</span>";
    }

    return $str;
}

//设置公告状态
function a_status($show_status)
{
    if ($show_status == 1) {
        $str = "<span class='label label-success radius'>开启</span>";
    } else {
        $str = "<span style='color: red;' class='label label-success radius'>未开启</span>";
    }

    return $str;
}

//供货商品推荐状态
function supply_pro_recommend($is_recommend)
{
    if ($is_recommend == 1) {
        $str = "<span style='color:red;' class='label label-success radius'>已推荐</span>";
    } else {
        $str = "<span class='label label-success radius'>未推荐</span>";
    }

    return $str;
}

//设置订单状态
function order_status($status){
    if($status == 2){
        $str = "<span class='label label-success radius'>未付款</span>";
    }elseif($status == 3){
        $str = "<span class='label label-success radius'>已付款</span>";
    }elseif($status == 4){
        $str = "<span class='label label-success radius'>已发货</span>";
    } else{
        $str = "<span class='label label-success radius'>已收货</span>";
    }

    return $str;
}