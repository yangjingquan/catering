<?php
namespace app\common\model;
use think\Exception;
use think\Model;
use think\Db;
class SupplyProducts extends Model{

    //查询商品
    public function getAllProducts($limit, $offset, $date_from, $date_to,$pro_name){
        $bis_id = session('bis_id','','bis');
        $where = " pro.status <> -1";
        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and pro.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and pro.create_time < '$new_date_to'";
        }
        if($pro_name){
            $where .= " and pro.p_name like '%$pro_name%'";
        }

        $listorder = [
            'pro.id'  => 'desc'
        ];
        $res = Db::table('store_supply_products')->alias('pro')->field('pro.id,pro.p_name,pro.original_price,pro.associator_price,pro.rec_rate,pro.on_sale,pro.status,pro.is_recommend,pro.sold,pro.inventory,pro.supply_price')
            ->where($where)
            ->order($listorder)
            ->limit($offset,$limit)
            ->select();

        //设置返回数据
        $index = 0;
        $result = array();
        foreach ($res as $item) {
            $result[$index]['id'] = $item['id'];
            $result[$index]['p_name'] = $item['p_name'];
            $result[$index]['inventory'] = $item['inventory'];
            $result[$index]['original_price'] = $item['original_price'];
            $result[$index]['associator_price'] = $item['associator_price'];
            $result[$index]['rec_rate'] = $item['rec_rate'] * 100 .'%';
            $result[$index]['on_sale'] = $item['on_sale'];
            $result[$index]['status'] = $item['status'];
            $result[$index]['is_recommend'] = $item['is_recommend'];
            $result[$index]['sold'] = $item['sold'];
            $result[$index]['supply_price'] = $item['supply_price'];
            $result[$index]['is_copied'] = $this->checkIsCopied($item['id'],$bis_id);

            $index ++;
        }
        return $result;
    }

    //查询商品数量
    public function getAllProductCount($date_from = '',$date_to = '',$pro_name = ''){
        $where = " status <> -1";

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and pro.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and pro.create_time < '$new_date_to'";
        }
        if($pro_name){
            $where .= " and pro.p_name like '%$pro_name%'";
        }

        $res = Db::table('store_supply_products')->where($where)->count();
        return $res;
    }



    //通过id获取商品信息
    public function getProInfoById($id){
        //设置查询条件
        $where = [
            'id'  => $id
        ];
        $res = Db::table('store_supply_products')->where($where)->find();

        $res['pro_id'] = $res['id'];
        $res['rec_rate'] = $res['rec_rate'] * 100;
        $images = json_decode($res['images'],true);
        $res['images'] = $images;
        return $res;
    }

    //复制
    public function copy($pro_id,$bis_id,$pro_type){
        //获取当前商品信息
        $proRes = $this->getProInfoById($pro_id);

        //复制供货商品到商家的商品表,商品图片表,商品配置表中
        Db::startTrans();
        try{
            //设置商品表字段
            $proData = array(
                'bis_id'  => $bis_id,
                'p_name'  => $proRes['p_name'],
                'on_sale'  => 0,
                'unit'  => $proRes['unit'],
                'producing_area'  => $proRes['producing_area'],
                'original_price'  => $proRes['original_price'],
                'associator_discount'  => $proRes['associator_discount'],
                'associator_price'  => $proRes['associator_price'],
                'supply_price'  => $proRes['supply_price'],
                'sold'  => $proRes['sold'],
                'listorder'  => $proRes['listorder'],
                'weight'  => $proRes['weight'],
                'huohao'  => $proRes['huohao'],
                'keywords'  => $proRes['keywords'],
                'introduce'  => $proRes['introduce'],
                'wx_introduce'  => $proRes['wx_introduce'],
                'is_recommend'  => $proRes['is_recommend'],
                'is_jf_product'  => $pro_type == 'ori' ? 0 : 1,
                'inventory'  => $proRes['inventory'],
                'jifen'  => !empty($proRes['jifen']) ? $proRes['jifen'] : 0,
                'supply_pro_id'  => $pro_id,
                'status'  => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            );
            $proId = Db::table('cy_mall_products')->insertGetId($proData);

            //图片信息
            $imagesArr = $proRes['images'];
            $imagesArr['p_id'] = $proId;
            $imagesArr['create_time'] = date('Y-m-d H:i:s');
            $imagesArr['update_time'] = date('Y-m-d H:i:s');

            $proImgRes = Db::table('cy_mall_pro_images')->insert($imagesArr);

            //商品配置信息
            $configs = $proRes['configs'];
            $configArr = json_decode($configs,true);

            foreach ($configArr as $item) {
                $cfgArr[] = [
                    'pro_id'  => $proId ,
                    'content1_name'  => $item['content1_name'],
                    'con_content1'  => $item['con_content1'],
                    'price'  => $item['price'],
                    'inventory'  => $item['inventory'],
                    'group_price'  => !empty($item['group_price']) ? $item['group_price'] : '0.00'
                ];
            }

            $cfgRes = Db::table('cy_mall_pro_config')->insertAll($cfgArr);

            Db::commit();
        }catch(Exception $exception){
            Db::rollback();
            return false;
        }

        return true;
    }

    //校验是否已经复制过该商品
    public function checkIsCopied($pro_id,$bis_id){
        $res = Db::table('cy_mall_products')->where('bis_id = '.$bis_id.' and status != -1 and is_jf_product = 1 and supply_pro_id = '.$pro_id)->find();
        if($res){
            return true;
        }else{
            return false;
        }
    }
}

?>