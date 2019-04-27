<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Advertisement extends Model{

    public function getAds($bis_id,$limit,$offset){
        $where = [
            'ads.bis_id'  => $bis_id,
            'ads.status'  => 1
        ];

        $order = [
            'ads.listorder'  => 'desc',
            'ads.created_time'  =>  'desc'
        ];

        $res = Db::table('cy_ads')->alias('ads')->field('ads.*')
            ->join('cy_bis bis','bis.id = ads.bis_id','left')
            ->where($where)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $res;
    }

    public function getAdsCount($bis_id){
        $where = [
            'ads.bis_id'  => $bis_id,
            'ads.status'  => 1
        ];

        $count = Db::table('cy_ads')->alias('ads')
            ->join('cy_bis bis','bis.id = ads.bis_id','left')
            ->where($where)
            ->count();

        return $count;
    }

    public function getAdInfoById($id){
        $where = [
            'id'  => $id
        ];

        $res = Db::table('cy_ads')->where($where)->find();

        return $res;
    }

}

?>