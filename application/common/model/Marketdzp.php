<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Marketdzp extends Model{

    //获取所有获奖信息
    public function getAllJiangpin($bis_id,$limit,$offset){
        $where = [
            'bis_id'  => $bis_id
        ];

        $listorder = [
            'id'  => 'desc'
        ];

        $res = Db::table('cy_dzp_user_jiangpin')->where($where)->order($listorder)->limit($offset,$limit)->select();
        return $res;
    }

    //获取所有奖品数量
    public function getAllJiangpinCount($bis_id){
        $where = [
            'bis_id'  => $bis_id
        ];
        $res = Db::table('cy_dzp_user_jiangpin')->where($where)->count();
        return $res;
    }

     //获取所有奖品设置信息
    public function getAllJp($bis_id,$limit,$offset){
        $where = [
            'status'  => ['neq','-1'],
            'bis_id'  => $bis_id
        ];

        $listorder = [
            'id'  => 'desc'
        ];

        $res = Db::table('cy_dzp_jiangpin')->where($where)->order($listorder)->limit($offset,$limit)->select();
        return $res;
    }

    //获取所有奖品设置总数
    public function getAllJpCount($bis_id){
        $where = [
            'status'  => ['neq','-1'],
            'bis_id'  => $bis_id
        ];

        $res = Db::table('cy_dzp_jiangpin')->where($where)->count();
        return $res;
    }

    

   

}

?>