<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Table extends Model{

    //添加桌位
    public function add($data){
        $data['status'] = 1;
        return Db::table('cy_tables')->insertGetId($data);
    }

    //获取所有桌位信息
    public function getTables($bis_id,$limit,$offset){
        $data = [
            'status'  => ['neq',-1],
            'bis_id'   => $bis_id
        ];

        $order = [
            'id'  => 'asc'
        ];

        $result = Db::table('cy_tables')
            ->where($data)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $result;
    }

    //获取所有桌位数量
    public function getTablesCount($bis_id){
        $data = [
            'status'  => ['neq',-1],
            'bis_id'   => $bis_id
        ];

        $order = [
            'id'  => 'asc'
        ];

        $result = Db::table('cy_tables')->where($data)->count();

        return $result;
    }
}

?>