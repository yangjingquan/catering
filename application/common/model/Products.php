<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Products extends Model{

    //添加商品
    public function add($data){
        $res = Db::table('cy_products')->insertGetId($data);
        return $res;
    }

    //查询商品
    public function getAllProducts($bis_id,$limit, $offset, $date_from, $date_to,$pro_name){
        $where = " pro.status <> -1 and pro.bis_id = ".$bis_id;

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
        $res = Db::table('cy_products')->alias('pro')->field('pro.*,cat.cat_name')
                ->join('cy_category cat','pro.cat_id = cat.id','LEFT')
                ->where($where)
                ->order($listorder)
                ->limit($offset,$limit)
                ->select();

        $index = 0;
        foreach($res as $val){
            switch($val['pro_type']){
                case 1:
                    $type = '堂食';
                    break;
                case 2:
                    $type = '外卖';
                    break;
                default:
                    $type = '堂食&外卖';
                    break;
            }
            $res[$index]['type'] = $type;
            $index ++;
        }
        return $res;
    }

    //查询商品数量
    public function getAllProductCount($bis_id,$date_from,$date_to,$pro_name){
        $where = " status <> -1 and bis_id = ".$bis_id;

        if($date_from){
            $where .= " and create_time >= '$date_from'";
        }
        if($date_to){
            $where .= " and create_time < '$date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $res = Db::table('cy_products')->where($where)->count();
        return $res;
    }

    //通过id获取商品信息
    public function getProInfoById($id){
        //设置查询条件
        $where = [
            'pro.id'  => $id
        ];
        $res = Db::table('cy_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,pro.cat_id,pro.original_price,pro.associator_price,pro.introduce as intro,pro.image,pro.pro_type,pro.detail_images')
            ->where($where)
            ->find();

        return $res;
    }

    //供货商品列表
    public function getAllSupplyProducts($bis_id,$limit, $offset, $date_from, $date_to,$pro_name){
        $where = " pro.status <> -1 ";

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
        $res = Db::table('cy_supply_products')->alias('pro')->field('pro.*,cat.cat_name')
            ->join('cy_category cat','pro.cat_id = cat.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset,$limit)
            ->select();

        $index = 0;
        foreach($res as $val){
            switch($val['pro_type']){
                case 1:
                    $type = '堂食';
                    break;
                case 2:
                    $type = '外卖';
                    break;
                default:
                    $type = '堂食&外卖';
                    break;
            }
            $res[$index]['type'] = $type;
            $res[$index]['is_copied'] = $this->checkIsCopied($val['id'],$bis_id);
            $index ++;
        }
        return $res;
    }

    //供货商品数量
    public function getAllSupplyProductCount($date_from,$date_to,$pro_name){
        $where = " status <> -1 ";

        if($date_from){
            $where .= " and create_time >= '$date_from'";
        }
        if($date_to){
            $where .= " and create_time < '$date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $res = Db::table('cy_supply_products')->where($where)->count();
        return $res;
    }

    //校验商品是否已经复制过该商品
    public function checkIsCopied($pro_id,$bis_id){
        $res = Db::table('cy_products')->where('bis_id = '.$bis_id.' and status != -1  and supply_pro_id = '.$pro_id)->find();
        if($res){
            return true;
        }else{
            return false;
        }
    }

    //复制
    public function copy($pro_id,$bis_id){
        //获取当前商品信息
        $proRes = $this->getSupplyProductById($pro_id);

        //复制供货商品到商家的商品表
        //设置商品表字段
        $proData = array(
            'bis_id'  => $bis_id,
            'p_name'  => $proRes['p_name'],
            'on_sale'  => $proRes['on_sale'],
            'cat_id'  => $proRes['cat_id'],
            'image'  => $proRes['image'],
            'original_price'  => $proRes['original_price'],
            'associator_price'  => $proRes['associator_price'],
            'supply_price'  => $proRes['supply_price'],
            'sold'  => $proRes['sold'],
            'introduce'  => $proRes['introduce'],
            'is_recommend'  => $proRes['is_recommend'],
            'pro_type'  => $proRes['pro_type'],
            'detail_images'  => $proRes['detail_images'],
            'supply_pro_id'  => $pro_id,
            'status'  => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        );
        $proId = Db::table('cy_products')->insertGetId($proData);

        return $proId;
    }

    //根据id获取供货商品信息
    public function getSupplyProductById($id){
        $where = [
            'id'  => $id
        ];
        $res = Db::table('cy_supply_products')->where($where)->find();

        return $res;
    }

}

?>