<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Validate;
use think\Db;

class Product extends Base {

    const PAGE_SIZE = 20;

    //商品列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $pro_name = input('get.pro_name','','');

        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        $pro_count = model('Products')->getAllProductCount($bis_id,$date_from,$date_to,$pro_name);
        //总页码
        $pages = ceil($pro_count / $limit);

        $pro_res = model('Products')->getAllProducts($bis_id,$limit, $offset,$date_from,$date_to,$pro_name);

        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'pages'  => $pages,
            'count'  => $pro_count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'pro_name'  => $pro_name
        ]);
    }

    //添加商品页面
    public function add(){
        $bis_id = session('bis_id','','bis');
        $year = date("Y");
        $category = model('Category')->getNormalFirstCategory($bis_id);
        return $this->fetch('',[
            'year'  => $year,
            'category'  => $category
        ]);
    }


    //编辑商品
    public function edit(){
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id','','bis');
        //获取该商品信息
        $pro_res = model('Products')->getProInfoById($id);
        $category = model('Category')->getNormalFirstCategory($bis_id);
        $temp_detail_images = json_decode($pro_res['detail_images'],true);
        for($i = 0;$i < 10;$i++){
            $detail_images[] = !empty($temp_detail_images[$i]) ? $temp_detail_images[$i] : '';
        }
        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'category'  => $category,
            'pro_id'  => $id,
            'detail_images'  => $detail_images,
            'no_img_url'  => self::NO_IMG_URL,
        ]);
    }

    //添加商品
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $bis_id = session('bis_id','','bis');

        //上传图片相关
        $image = new Image();

        if($_FILES['image']['error'] == 0){
            $image_data = $image->uploadS('image','product');
            $image_data = self::IMG_URL.str_replace("\\", "/", $image_data);
        }else{
            return $this->error('图片必须设置!');
        }

        $wx_config_error = $_FILES['wx_config_image']['error'];
        $wx_is_pass = false;
        foreach($wx_config_error as $val){
            if($val == 0){
                $wx_is_pass = true;
                break;
            }
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
        $wx_config_images_json = json_encode($wx_config_data);

        //设置添加到数据库的数据
        $product_data = [
            'bis_id' => $bis_id,
            'cat_id' => $param['cat_id'],
            'p_name' => $param['p_name'],
            'pro_type' => $param['pro_type'],
            'original_price' => $param['original_price'],
            'associator_price' => $param['associator_price'],
            'image' => $image_data,
            'introduce' => $param['intro'],
            'detail_images' => $wx_config_images_json,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //普通数据添加到商品表中
        $p_res = model('Products')->add($product_data);


        if($p_res){
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
        $res = Db::table('cy_products')->where('id = '.$id)->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    //更改上架状态
    public function updateSaleStatus(){
        //获取参数
        $id = input('get.id');
        $on_sale = input('get.on_sale');
        $data['on_sale'] = $on_sale;
        $res = Db::table('cy_products')->where('id = '.$id)->update($data);

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
        $res = Db::table('cy_products')->where('id = '.$id)->update($data);

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

    //修改商品
    public function updateProduct(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //获取当前商品信息
        $res = Db::table('cy_products')->where("id = ".$param['pro_id'])->find();
        $detail_images_json = $res['detail_images'];
        $detail_images = json_decode($detail_images_json,true);
        //上传图片相关
        $image = new Image();

        //设置更新的数据
        $product_data = [
            'cat_id' => $param['cat_id'],
            'p_name' => $param['p_name'],
            'pro_type' => $param['pro_type'],
            'original_price' => $param['original_price'],
            'associator_price' => $param['associator_price'],
            'introduce' => $param['intro'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        //设置图片
        if($_FILES['image']['error'] == 0){
            $upload_image = $image->uploadS('image','product');
            $upload_image = str_replace("\\", "/", $upload_image);
            $product_data['image'] = self::IMG_URL.$upload_image;
        }

        if($_FILES['wx_config_image1']['error'] == 0){
            $images_data['wx_config_image1'] = $image->uploadS('wx_config_image1','product');
            $detail_images[0] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image1']);
        }
        if($_FILES['wx_config_image2']['error'] == 0){
            $images_data['wx_config_image2'] = $image->uploadS('wx_config_image2','product');
            $detail_images[1] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image2']);
        }
        if($_FILES['wx_config_image3']['error'] == 0){
            $images_data['wx_config_image3'] = $image->uploadS('wx_config_image3','product');
            $detail_images[2] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image3']);
        }
        if($_FILES['wx_config_image4']['error'] == 0){
            $images_data['wx_config_image4'] = $image->uploadS('wx_config_image4','product');
            $detail_images[3] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image4']);
        }
        if($_FILES['wx_config_image5']['error'] == 0){
            $images_data['wx_config_image5'] = $image->uploadS('wx_config_image5','product');
            $detail_images[4] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image5']);
        }
        if($_FILES['wx_config_image6']['error'] == 0){
            $images_data['wx_config_image6'] = $image->uploadS('wx_config_image6','product');
            $detail_images[5] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image6']);
        }
        if($_FILES['wx_config_image7']['error'] == 0){
            $images_data['wx_config_image7'] = $image->uploadS('wx_config_image7','product');
            $detail_images[6] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image7']);
        }
        if($_FILES['wx_config_image8']['error'] == 0){
            $images_data['wx_config_image8'] = $image->uploadS('wx_config_image8','product');
            $detail_images[7] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image8']);
        }
        if($_FILES['wx_config_image9']['error'] == 0){
            $images_data['wx_config_image9'] = $image->uploadS('wx_config_image9','product');
            $detail_images[8] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image9']);
        }
        if($_FILES['wx_config_image10']['error'] == 0){
            $images_data['wx_config_image10'] = $image->uploadS('wx_config_image10','product');
            $detail_images[9] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image10']);
        }
        $detail_images_json = json_encode($detail_images);
        $product_data['detail_images'] = $detail_images_json;
        //更新商品表
        $p_res = Db::table('cy_products')->where('id = '.$param['pro_id'])->update($product_data);

        $this->success("修改成功!");
    }

}
