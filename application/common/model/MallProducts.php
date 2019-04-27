<?php
namespace app\common\model;
use think\Model;
use think\Db;
class MallProducts extends Model{

    //添加商品
    public function add($data){
        $res = Db::table('cy_mall_products')->insertGetId($data);

        return $res;
    }

    //查询积分商品
    public function getAllProducts($bis_id,$limit, $offset, $date_from, $date_to,$pro_name){
        $where = " is_jf_product = 1 and status <> -1 and bis_id = ".$bis_id;

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and create_time < '$new_date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $listorder = [
            'id'  => 'desc'
        ];
        $res = Db::table('cy_mall_products')->where($where)->order($listorder)->limit($offset,$limit)->select();

        //设置返回数据
        $index = 0;
        $result = array();
        foreach ($res as $item) {
            $result[$index]['id'] = $item['id'];
            $result[$index]['p_name'] = $item['p_name'];
            $result[$index]['inventory'] = $this->getInventoryCount($item['id']);
            $result[$index]['original_price'] = $item['original_price'];
            $result[$index]['associator_price'] = $item['associator_price'];
            $result[$index]['ex_jifen'] = floor($item['ex_jifen']);
            $result[$index]['on_sale'] = $item['on_sale'];
            $result[$index]['status'] = $item['status'];
            $result[$index]['is_recommend'] = $item['is_recommend'];
            $result[$index]['is_copied'] = empty($item['supply_pro_id']) ? 0 : 1;

            $index ++;
        }
        return $result;
    }

    //查询积分商品数量
    public function getAllProductCount($bis_id,$date_from,$date_to,$pro_name){
        $where = " is_jf_product = 1 and status <> -1 and bis_id = ".$bis_id;

        if($date_from){
            $where .= " and create_time >= '$date_from'";
        }
        if($date_to){
            $where .= " and create_time < '$date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $res = Db::table('cy_mall_products')->where($where)->count();
        return $res;
    }

    //通过id获取商品信息
    public function getProInfoById($id){
        //设置查询条件
        $where = [
            'pro.id'  => $id
        ];
        $res = Db::table('cy_mall_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,pro.unit,pro.producing_area,pro.ex_jifen,pro.original_price,pro.associator_discount,pro.associator_price,weight,pro.listorder,pro.huohao,pro.keywords,pro.introduce,pro.supply_pro_id,pro.wx_introduce,img.image,img.thumb,img.config_image1,img.config_image2,img.config_image3,img.config_image4,img.wx_config_image1,img.wx_config_image2,img.wx_config_image3,img.wx_config_image4,img.wx_config_image5,img.wx_config_image6,img.wx_config_image7,img.wx_config_image8,img.wx_config_image9,img.wx_config_image10')
            ->join('cy_mall_pro_images img','pro.id = img.p_id','LEFT')
            ->where($where)
            ->find();
        return $res;
    }

    //查询库存
    public function getInventoryCount($pro_id){
        $res = Db::table('cy_mall_pro_config')->where('pro_id = '.$pro_id.' and status = 1')->SUM('inventory');
        return $res;
    }

    //查询配置信息
    public function getConfigInfo($pro_id){
        $res = Db::table('cy_mall_pro_config')->where('pro_id = '.$pro_id.' and status = 1')->select();
        return $res;
    }
}

?>