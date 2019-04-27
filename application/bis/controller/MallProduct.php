<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Exception;
use think\Validate;
use think\Db;

class MallProduct extends Base {

    const PAGE_SIZE = 20;

    //积分商品列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $pro_name = input('get.pro_name');

        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $pro_count = model('MallProducts')->getAllProductCount($bis_id,$date_from,$date_to,$pro_name);
        //总页码
        $pages = ceil($pro_count / $limit);

        $pro_res = model('MallProducts')->getAllProducts($bis_id,$limit, $offset,$date_from,$date_to,$pro_name);

        return $this->fetch('mall_product/index',[
            'pro_res'  => $pro_res,
            'pages'  => $pages,
            'count'  => $pro_count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'pro_name'  => $pro_name,
        ]);
    }

    //添加积分商品页面
    public function add(){
        $year = date("Y");
        return $this->fetch('',[
            'year'  => $year
        ]);
    }

    //编辑积分商品
    public function edit(){
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id','','bis');
        //获取该商品信息
        $pro_res = model('MallProducts')->getProInfoById($id);
        $pro_config = model('MallProducts')->getConfigInfo($id);
        $pro_config_count = count($pro_config);

        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'pro_config'  => $pro_config,
            'pro_config_count'  => $pro_config_count,
            'pro_id'  => $id,
            'no_img_url'  => self::NO_IMG_URL

        ]);
    }

    //添加积分商品
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $bis_id = session('bis_id','','bis');

        //验证数据
        $validate = validate('Product');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();
        $config_error = $_FILES['config_image']['error'];
        $wx_config_error = $_FILES['wx_config_image']['error'];
        $is_pass = false;
        $wx_is_pass = false;
        foreach($config_error as $val){
            if($val == 0){
                $is_pass = true;
                break;
            }
        }
        foreach($wx_config_error as $val){
            if($val == 0){
                $wx_is_pass = true;
                break;
            }
        }

        if($is_pass){
            $config_data_temp = $image->uploadM('config_image','product');
            $config_data = array();
            foreach($config_data_temp as $val){
                $con_image = self::IMG_URL.str_replace("\\", "/", $val);
                array_push($config_data,$con_image);
            }
        }else{
            $config_data = array();
        }

        if($wx_is_pass){
            $wx_config_data_temp = $image->uploadM('wx_config_image','product');
            $wx_config_data = array();
            foreach($wx_config_data_temp as $val){
                $con_image = self::IMG_URL.str_replace("\\", "/", $val);
                array_push($wx_config_data,$con_image);
            }
        }else{
            $wx_config_data = array();
        }

        if($_FILES['image']['error'] == 0){
            $image_data = $image->uploadS('image','product');
            $image_data = self::IMG_URL.str_replace("\\", "/", $image_data);
        }else{
            return $this->error('大图必须设置!');
        }

        if($_FILES['thumb']['error'] == 0){
            $thumb_data = $image->uploadS('thumb','product');
            $thumb_data = self::IMG_URL.str_replace("\\", "/", $thumb_data);
        }else{
            return $this->error('小图必须设置!');
        }

        //设置添加到数据库的数据
        $product_data = [
            'bis_id' => $bis_id,
            'p_name' => $param['p_name'],
            'unit' => $param['unit'],
            'producing_area' => $param['producing_area'],
            'original_price' => $param['original_price'],
            'associator_discount' => $param['associator_discount'],
            'associator_price' => $param['associator_price'],
            'ex_jifen' => $param['ex_jifen'],
            'listorder' => $param['listorder'],
            'weight' => $param['weight'],
            'huohao' => $param['huohao'],
            'keywords' => $param['keywords'],
            'wx_introduce' => $param['wx_description'],
            'is_jf_product' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //设置商品配置信息
        $index = 0;
        $totalInventory = 0;
        foreach($param['content1_name'] as $val){
            $totalInventory += $param['inventory'][$index];
            $index ++;
        }
        $product_data['inventory'] = $totalInventory;
        //普通数据添加到商品表中
        $p_res = model('MallProducts')->add($product_data);

        //设置上传图片的内容
        if(!empty($config_data)){
            $config_image1 = !empty($config_data[0]) ? $config_data[0] : '';
            $config_image2 = !empty($config_data[1]) ? $config_data[1] : '';
            $config_image3 = !empty($config_data[2]) ? $config_data[2] : '';
            $config_image4 = !empty($config_data[3]) ? $config_data[3] : '';
        }else{
            $config_image1 = $config_image2 = $config_image3 = $config_image4 = '';
        }
        //设置小程序上传图片的内容
        if(!empty($wx_config_data)){
            $wx_config_image1 = !empty($wx_config_data[0]) ? $wx_config_data[0] : '';
            $wx_config_image2 = !empty($wx_config_data[1]) ? $wx_config_data[1] : '';
            $wx_config_image3 = !empty($wx_config_data[2]) ? $wx_config_data[2] : '';
            $wx_config_image4 = !empty($wx_config_data[3]) ? $wx_config_data[3] : '';
            $wx_config_image5 = !empty($wx_config_data[4]) ? $wx_config_data[4] : '';
            $wx_config_image6 = !empty($wx_config_data[5]) ? $wx_config_data[5] : '';
            $wx_config_image7 = !empty($wx_config_data[6]) ? $wx_config_data[6] : '';
            $wx_config_image8 = !empty($wx_config_data[7]) ? $wx_config_data[7] : '';
            $wx_config_image9 = !empty($wx_config_data[8]) ? $wx_config_data[8] : '';
            $wx_config_image10 = !empty($wx_config_data[9]) ? $wx_config_data[9] : '';
        }else{
            $wx_config_image1 = $wx_config_image2 = $wx_config_image3 = $wx_config_image4 = $wx_config_image5 = $wx_config_image6 = $wx_config_image7 = $wx_config_image8 = $wx_config_image9 = $wx_config_image10 = '';
        }

        $images_data = [
            'p_id'  => $p_res,
            'image'  =>  $image_data,
            'thumb'  => $thumb_data,
            'config_image1'  => $config_image1,
            'config_image2'  => $config_image2,
            'config_image3'  => $config_image3,
            'config_image4'  => $config_image4,
            'wx_config_image1'  => $wx_config_image1,
            'wx_config_image2'  => $wx_config_image2,
            'wx_config_image3'  => $wx_config_image3,
            'wx_config_image4'  => $wx_config_image4,
            'wx_config_image5'  => $wx_config_image5,
            'wx_config_image6'  => $wx_config_image6,
            'wx_config_image7'  => $wx_config_image7,
            'wx_config_image8'  => $wx_config_image8,
            'wx_config_image9'  => $wx_config_image9,
            'wx_config_image10'  => $wx_config_image10,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //添加图片数据到数据表
        $image_res = Db::table('cy_mall_pro_images')->insert($images_data);

        $ind = 0;
        foreach($param['content1_name'] as $val){
            $p_config_data[] = [
                'pro_id'  => $p_res,
                'content1_name'  => $param['content1_name'][$ind],
                'con_content1'  => $param['con_content1'][$ind],
                'ex_jifen'  => $param['ex_jifen'][$ind],
                'price'  => $param['unit_price'][$ind],
                'inventory'  => $param['inventory'][$ind],
                'group_price'  => !empty($param['group_price'][$ind]) ? $param['group_price'][$ind] : '0.00'
            ];
            $ind ++;
        }

        $config_res = Db::table('cy_mall_pro_config')->insertAll($p_config_data);

        if($p_res && $image_res){
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
        $res = Db::table('cy_mall_products')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //更改上架状态
    public function updateSaleStatus(){
        //获取参数
        $id = input('get.id');
        $on_sale = input('get.on_sale');
        $data['on_sale'] = $on_sale;

        Db::startTrans();
        try{
            if($on_sale == 1){
                //判断是否是供货商品
                $pro_res = model('MallProducts')->getProInfoById($id);
                $supplyProId = $pro_res['supply_pro_id'];

                if(!empty($supplyProId) || $supplyProId != 0 || $supplyProId != ''){
                    //获取供货商品上下架状态
                    $supplyProInfo = Model('SupplyProducts')->getProInfoById($supplyProId);
                    $supply_pro_on_sale = $supplyProInfo['on_sale'];
                    if($supply_pro_on_sale == 0){
                        throw new Exception('供货商品已下架');
                    }
                }
            }
            $res = Db::table('cy_mall_products')->where('id = '.$id)->update($data);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }

        if($res){
            if($on_sale == 1){
                $this->success('上架成功!');
            }else{
                $this->success('下架成功!');
            }
        }else{
            $this->error('更新状态失败!');
        }
    }


    //更改推荐状态
    public function updateRecommendStatus(){
        //获取参数
        $id = input('get.id');
        $is_recommend = input('get.is_recommend');
        $data['is_recommend'] = $is_recommend;
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = Db::table('cy_mall_products')->where('id = '.$id)->update($data);

        if($res){
            if($is_recommend == 1){
                $this->success('设置推荐成功!');
            }else{
                $this->success('取消推荐成功!');
            }
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改积分商品
    public function updateProduct(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //验证数据
        $validate = validate('Product');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();

        //设置图片
        if($_FILES['config_image1']['error'] == 0){
            $images_data['config_image1'] = $image->uploadS('config_image1','product');
            $images_data['config_image1'] = self::IMG_URL.str_replace("\\", "/", $images_data['config_image1']);
        }
        if($_FILES['config_image2']['error'] == 0){
            $images_data['config_image2'] = $image->uploadS('config_image2','product');
            $images_data['config_image2'] = self::IMG_URL.str_replace("\\", "/", $images_data['config_image2']);
        }
        if($_FILES['config_image3']['error'] == 0){
            $images_data['config_image3'] = $image->uploadS('config_image3','product');
            $images_data['config_image3'] = self::IMG_URL.str_replace("\\", "/", $images_data['config_image3']);
        }
        if($_FILES['config_image4']['error'] == 0){
            $images_data['config_image4'] = $image->uploadS('config_image4','product');
            $images_data['config_image4'] = self::IMG_URL.str_replace("\\", "/", $images_data['config_image4']);
        }
        if($_FILES['wx_config_image1']['error'] == 0){
            $images_data['wx_config_image1'] = $image->uploadS('wx_config_image1','product');
            $images_data['wx_config_image1'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image1']);
        }
        if($_FILES['wx_config_image2']['error'] == 0){
            $images_data['wx_config_image2'] = $image->uploadS('wx_config_image2','product');
            $images_data['wx_config_image2'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image2']);
        }
        if($_FILES['wx_config_image3']['error'] == 0){
            $images_data['wx_config_image3'] = $image->uploadS('wx_config_image3','product');
            $images_data['wx_config_image3'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image3']);
        }
        if($_FILES['wx_config_image4']['error'] == 0){
            $images_data['wx_config_image4'] = $image->uploadS('wx_config_image4','product');
            $images_data['wx_config_image4'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image4']);
        }
        if($_FILES['wx_config_image5']['error'] == 0){
            $images_data['wx_config_image5'] = $image->uploadS('wx_config_image5','product');
            $images_data['wx_config_image5'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image5']);
        }
        if($_FILES['wx_config_image6']['error'] == 0){
            $images_data['wx_config_image6'] = $image->uploadS('wx_config_image6','product');
            $images_data['wx_config_image6'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image6']);
        }
        if($_FILES['wx_config_image7']['error'] == 0){
            $images_data['wx_config_image7'] = $image->uploadS('wx_config_image7','product');
            $images_data['wx_config_image7'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image7']);
        }
        if($_FILES['wx_config_image8']['error'] == 0){
            $images_data['wx_config_image8'] = $image->uploadS('wx_config_image8','product');
            $images_data['wx_config_image8'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image8']);
        }
        if($_FILES['wx_config_image9']['error'] == 0){
            $images_data['wx_config_image9'] = $image->uploadS('wx_config_image9','product');
            $images_data['wx_config_image9'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image9']);
        }
        if($_FILES['wx_config_image10']['error'] == 0){
            $images_data['wx_config_image10'] = $image->uploadS('wx_config_image10','product');
            $images_data['wx_config_image10'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image10']);
        }
        if($_FILES['image']['error'] == 0){
            $images_data['image'] = $image->uploadS('image','product');
            $images_data['image'] = self::IMG_URL.str_replace("\\", "/", $images_data['image']);
        }
        if($_FILES['thumb']['error'] == 0){
            $images_data['thumb'] = $image->uploadS('thumb','product');
            $images_data['thumb'] = self::IMG_URL.str_replace("\\", "/", $images_data['thumb']);
        }

        //设置更新的数据
        $product_data = [
            'p_name' => $param['p_name'],
            'unit' => $param['unit'],
            'producing_area' => $param['producing_area'],
            'original_price' => $param['original_price'],
            'associator_discount' => $param['associator_discount'],
            'associator_price' => $param['associator_price'],
            'ex_jifen' => $param['ex_jifen'],
            'listorder' => $param['listorder'],
            'weight' => $param['weight'],
            'huohao' => $param['huohao'],
            'keywords' => $param['keywords'],
            'wx_introduce' => $param['wx_description'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        //更新商品表
        $p_res = Db::table('cy_mall_products')->where('id = '.$param['pro_id'])->update($product_data);

        $images_data['update_time'] = date('Y-m-d H:i:s');

        //更新图片表
        $image_res = Db::table('cy_mall_pro_images')->where('p_id = '.$param['pro_id'])->update($images_data);


        $this->success("修改成功!");
    }

    //更新积分商品配置信息
    public function updateConfigInfo(){
        //获取参数
        $param = input('post.');

        $data = [
            'content1_name'   => $param['content1_name'],
            'con_content1'   => $param['con_content1'],
            'price'   => $param['unit_price'],
            'inventory'   => $param['inventory'],
            'ex_jifen'   => $param['ex_jifen'],
        ];

        $res = Db::table('cy_mall_pro_config')->where('id = '.$param['config_id'])->update($data);
        if($res){
            return show(1,'success','edit?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //添加积分商品配置信息
    public function addConfigInfo(){
        //获取参数
        $param = input('post.');

        $data = [
            'pro_id'   => $param['pro_id'],
            'content1_name'   => $param['content1_name'],
            'con_content1'   => $param['con_content1'],
            'price'   => $param['unit_price'],
            'inventory'   => $param['inventory'],
            'ex_jifen'   => $param['ex_jifen'],
        ];

        $res = Db::table('cy_mall_pro_config')->insert($data);
        if($res){
            return show(1,'success','edit?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //删除积分商品配置信息
    public function removeConfigInfo(){
        //获取参数
        $param = input('post.');

        $data = [
            'status'  => 0
        ];

        $res = Db::table('cy_mall_pro_config')->where('id = '.$param['config_id'])->update($data);
        if($res){
            return show(1,'success','edit?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //获取指定商品下的配置信息
    public function getConfigInfoByProid(){
        //获取参数
        $id = input('post.id');
        $pro_config = model('MallProducts')->getConfigInfo($id);

        if($pro_config){
            return show(1,'success',$pro_config);
        }else{
            return show(0,'error');
        }
    }

    //供货商品列表
    public function supplyIndex(){
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $pro_name = input('get.pro_name');

        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        $pro_count = model('SupplyProducts')->getAllProductCount($date_from,$date_to,$pro_name);
        //总页码
        $pages = ceil($pro_count / $limit);

        $pro_res = model('SupplyProducts')->getAllProducts($limit, $offset,$date_from,$date_to,$pro_name);

        return $this->fetch('mall_product/supply_index',[
            'pro_res'  => $pro_res,
            'pages'  => $pages,
            'count'  => $pro_count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'pro_name'  => $pro_name
        ]);
    }

    public function copy(){
        $pro_id = input('get.pro_id');
        $pro_type = input('get.pro_type');
        $bis_id = session('bis_id','','bis');
        try {
            //获取该商品信息
            $res = model('SupplyProducts')->copy($pro_id,$bis_id,$pro_type);
        }catch(Exception $e){
            $this->error($e->getMessage());
        }

        if($res){
            $this->success("复制成功!");
        }else{
            $this->success("复制失败!");
        }
    }

}
