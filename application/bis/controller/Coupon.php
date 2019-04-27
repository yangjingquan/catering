<?php
namespace app\bis\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Coupon extends Base {

    //优惠券列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
            $count = model('Coupon')->getCouponsCount($bis_id);

        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Coupon')->getCoupons($bis_id,$limit,$offset);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }

    //添加优惠券页面
    public function add(){
        return $this->fetch('');
    }

    //编辑优惠券
    public function edit(){
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id','','bis');
        //获取该分类信息
        $res = Db::table('cy_coupon')->where('id = '.$id)->find();

        $res['start_time'] = substr($res['start_time'],0,10);
        $res['end_time'] = substr($res['end_time'],0,10);

        return $this->fetch('',[
            'coupon'  => $res
        ]);
    }

    //添加优惠券
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => session('bis_id','','bis'),
            'coupon_name' => $param['coupon_name'],
            'max' => $param['max'],
            'par_value' => $param['par_value'],
            'start_time' => date('Y-m-d H:i:s',strtotime($param['start_time'])),
            'end_time' => date('Y-m-d H:i:s',strtotime($param['end_time'])+86399),
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        $res = model('Coupon')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }


    //更改状态
    public function updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('cy_coupon')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改优惠券
    public function updateCoupon(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        $param = input('post.');
        //设置添加到数据库的数据
        $data = [
            'coupon_name' => $param['coupon_name'],
            'max' => $param['max'],
            'par_value' => $param['par_value'],
            'start_time' => date('Y-m-d H:i:s',strtotime($param['start_time'])),
            'end_time' => date('Y-m-d H:i:s',strtotime($param['end_time'])+86399),
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_coupon')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }
}
