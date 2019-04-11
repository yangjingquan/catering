<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Members extends Model{

    //获取餐饮所有会员信息
    public function getAllMembers($bis_id,$limit,$offset){
        $where = [
            'mem.status'  => 1,
            'mem.bis_id'  => $bis_id
        ];

        $listorder = [
            'mem.id'  => 'desc'
        ];

        $res = Db::table('cy_members')->alias('mem')->field('mem.id,mem.bis_id,mem.username,mem.nickname,mem.create_time,bis.bis_name')
            ->join('cy_bis bis','mem.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset,$limit)
            ->select();

        return $res;
    }

    //获取餐饮所有会员数量
    public function getAllMembersCount($bis_id){
        $where = [
            'status'  => 1,
            'bis_id'  => $bis_id
        ];

        $res = Db::table('cy_members')->where($where)->count();

        return $res;
    }

}

?>