<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Banner extends Model{

    //添加banner
    public function add($data){
        $res = Db::table('cy_banners')->insert($data);
        return $res;
    }

    //获取所有banner信息
    public function getAllBanners($bis_id,$limit,$offset){
        $where = [
            'status'  => ['neq','-1'],
            'bis_id'  => $bis_id
        ];

        $listorder = [
            'listorder'  => 'desc',
            'id'  => 'desc'
        ];

        $res = Db::table('cy_banners')->where($where)->order($listorder)->limit($offset,$limit)->select();
        return $res;
    }

    //获取所有banner总数
    public function getAllBannersCount($bis_id){
        $where = [
            'status'  => ['neq','-1'],
            'bis_id'  => $bis_id
        ];

        $res = Db::table('cy_banners')->where($where)->count();
        return $res;
    }

    //根据id获取banner信息
    public function getRecInfoById($id){
        $where = [
            'id'  => $id
        ];

        $res = Db::table('cy_banners')->where($where)->find();
        return $res;
    }

}

?>