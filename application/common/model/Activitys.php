<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Activitys extends Model{

    //添加活动
    public function add($data){
        $data['status'] = 1;
        return Db::table('cy_activitys')->insert($data);
    }

    //获取所有活动信息
    public function getActivitys($bis_id,$limit,$offset){
        $data = [
            'status'  => ['neq',-1],
            'bis_id'   => $bis_id
        ];

        $order = [
            'update_time'  => 'desc'
        ];

        $result = Db::table('cy_activitys')
            ->where($data)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $result;
    }

    //获取所有活动数量
    public function getActivitysCount($bis_id){
        $data = [
            'status'  => ['neq',-1],
            'bis_id'   => $bis_id
        ];

        $order = [
            'update_time'  => 'desc'
        ];

        $result = Db::table('cy_activitys')->where($data)->count();

        return $result;
    }
}

?>