<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Db;

class Bis extends Base {

    //店铺信息
    public function index(){
        $bis_id = session('bis_id','','bis');
        //获取店铺信息
        $bis_res = model('Bis')->getBisInfo($bis_id);

        $citys = $bis_res['citys'];
        $citysArr = explode(',',$citys);
        $provinceId = $citysArr[0];
        $cityId = $citysArr[1];

        //省级信息
        $provinces = model('Province')->getProvinceInfo();
        //市级信息
        $citys = model('Province')->getCitysById($provinceId);

        return $this->fetch('',[
            'bis_res'  => $bis_res,
            'provinces'  => $provinces,
            'citys'  => $citys,
            'province_id'  => $provinceId,
            'city_id'  => $cityId,
        ]);
    }

    //修改店铺信息
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        $param = input('post.');

        //获取当前地址
        $provinceId = $param['city_id'];
        $cityId = $param['se_city_id'];
        $address = $param['address'];
        $province = $this->getProvinceInfo($provinceId);
        $city = $this->getCityInfo($cityId);
        $address = $province.$city.$address;
        $positionRes = $this->execUrl($address);
        $positionArr = json_decode($positionRes,true);

        if(!empty($positionArr['pois'])){
            $location = $positionArr['pois'][0]['location'];
        }else{
            $this->error('设置的地址检测不到经纬度,请重新设置');
        }

        //设置添加到数据库的数据
        $data = [
            'bis_name' => $param['bis_name'],
            'citys' => $param['city_id'].','.$param['se_city_id'],
            'address' => $param['address'],
            'brand' => $param['brand'],
            'leader' => $param['leader'],
            'link_tel' => $param['link_tel'],
            'link_mobile' => $param['link_mobile'],
            'email' => $param['email'],
            'intro' => $param['intro'],
            'scope' => $param['scope'],
            'is_pay' => $param['is_pay'],
            'appid' => $param['appid'],
            'mchid' => $param['mchid'],
            'key' => $param['key'],
            'notify_url' => $param['notify_url'],
            'catering_notify_url' => $param['catering_notify_url'],
            'reserve_notify_url' => $param['reserve_notify_url'],
            'collect_notify_url' => $param['collect_notify_url'],
            'recharge_notify_url' => $param['recharge_notify_url'],
            'secret' => $param['secret'],
            'logistics_status' => $param['logistics_status'],
            'min_price' => $param['min_price'],
            'lunch_box_fee' => $param['lunch_box_fee'],
            'distribution_fee' => $param['distribution_fee'],
            'jifen_ratio' => $param['jifen_ratio'],
            'mem_jifen_ratio' => $param['mem_jifen_ratio'],
            'business_time' => $param['business_time'],
            'positions' => $location,
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_bis')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

    //店铺图片设置页面
    public function img_index(){
        $bis_id = session('bis_id','','bis');
        $img_res = Db::table('cy_bis_images')->where('bis_id = '.$bis_id)->find();
        if(!$img_res){
            $img_res = [
                'logo_image'  => '',
                'env_image1'  => '',
                'env_image2'  => '',
                'env_image3'  => '',
                'env_image4'  => '',
                'env_image5'  => '',
                'env_image6'  => '',
                'qua_image1'  => '',
                'qua_image2'  => '',
                'qua_image3'  => '',
                'qua_image4'  => '',
                'qua_image5'  => '',
                'qua_image6'  => ''
            ];
        }
        return $this->fetch('',[
            'img_res'  => $img_res
        ]);
    }

    //设置图片
    public function saveImgs(){
        $bis_id = session('bis_id','','bis');

        //上传图片相关
        $image = new Image();

        //设置图片
        if($_FILES['logo_image']['error'] == 0){
            $images_data['logo_image'] = $image->uploadS('logo_image','bisImgs');
            $images_data['logo_image'] = self::IMG_URL.str_replace("\\", "/", $images_data['logo_image']);
        }
        if($_FILES['env_image1']['error'] == 0){
            $images_data['env_image1'] = $image->uploadS('env_image1','bisImgs');
            $images_data['env_image1'] = self::IMG_URL.str_replace("\\", "/", $images_data['env_image1']);
        }
        if($_FILES['env_image2']['error'] == 0){
            $images_data['env_image2'] = $image->uploadS('env_image2','bisImgs');
            $images_data['env_image2'] = self::IMG_URL.str_replace("\\", "/", $images_data['env_image2']);
        }
        if($_FILES['env_image3']['error'] == 0){
            $images_data['env_image3'] = $image->uploadS('env_image3','bisImgs');
            $images_data['env_image3'] = self::IMG_URL.str_replace("\\", "/", $images_data['env_image3']);
        }
        if($_FILES['env_image4']['error'] == 0){
            $images_data['env_image4'] = $image->uploadS('env_image4','bisImgs');
            $images_data['env_image4'] = self::IMG_URL.str_replace("\\", "/", $images_data['env_image4']);
        }
        if($_FILES['env_image5']['error'] == 0){
            $images_data['env_image5'] = $image->uploadS('env_image5','bisImgs');
            $images_data['env_image5'] = self::IMG_URL.str_replace("\\", "/", $images_data['env_image5']);
        }
        if($_FILES['env_image6']['error'] == 0){
            $images_data['env_image6'] = $image->uploadS('env_image6','bisImgs');
            $images_data['env_image6'] = self::IMG_URL.str_replace("\\", "/", $images_data['env_image6']);
        }
        if($_FILES['qua_image1']['error'] == 0){
            $images_data['qua_image1'] = $image->uploadS('qua_image1','bisImgs');
            $images_data['qua_image1'] = self::IMG_URL.str_replace("\\", "/", $images_data['qua_image1']);
        }
        if($_FILES['qua_image2']['error'] == 0){
            $images_data['qua_image2'] = $image->uploadS('qua_image2','bisImgs');
            $images_data['qua_image2'] = self::IMG_URL.str_replace("\\", "/", $images_data['qua_image2']);
        }
        if($_FILES['qua_image3']['error'] == 0){
            $images_data['qua_image3'] = $image->uploadS('qua_image3','bisImgs');
            $images_data['qua_image3'] = self::IMG_URL.str_replace("\\", "/", $images_data['qua_image3']);
        }
        if($_FILES['qua_image4']['error'] == 0){
            $images_data['qua_image4'] = $image->uploadS('qua_image4','bisImgs');
            $images_data['qua_image4'] = self::IMG_URL.str_replace("\\", "/", $images_data['qua_image4']);
        }
        if($_FILES['qua_image5']['error'] == 0){
            $images_data['qua_image5'] = $image->uploadS('qua_image5','bisImgs');
            $images_data['qua_image5'] = self::IMG_URL.str_replace("\\", "/", $images_data['qua_image5']);
        }
        if($_FILES['qua_image6']['error'] == 0){
            $images_data['qua_image6'] = $image->uploadS('qua_image6','bisImgs');
            $images_data['qua_image6'] = self::IMG_URL.str_replace("\\", "/", $images_data['qua_image6']);
        }

        $img_res = Db::table('cy_bis_images')->where('bis_id = '.$bis_id)->find();
        if(!$img_res){
            //设置添加的信息
            $insert_data = [
                'bis_id' => $bis_id,
                'logo_image' => !empty($images_data['logo_image']) ? $images_data['logo_image'] : '',
                'env_image1' => !empty($images_data['env_image1']) ? $images_data['env_image1'] : '',
                'env_image2' => !empty($images_data['env_image2']) ? $images_data['env_image2'] : '',
                'env_image3' => !empty($images_data['env_image3']) ? $images_data['env_image3'] : '',
                'env_image4' => !empty($images_data['env_image4']) ? $images_data['env_image4'] : '',
                'env_image5' => !empty($images_data['env_image5']) ? $images_data['env_image5'] : '',
                'env_image6' => !empty($images_data['env_image6']) ? $images_data['env_image6'] : '',
                'qua_image1' => !empty($images_data['qua_image1']) ? $images_data['qua_image1'] : '',
                'qua_image2' => !empty($images_data['qua_image2']) ? $images_data['qua_image2'] : '',
                'qua_image3' => !empty($images_data['qua_image3']) ? $images_data['qua_image3'] : '',
                'qua_image4' => !empty($images_data['qua_image4']) ? $images_data['qua_image4'] : '',
                'qua_image5' => !empty($images_data['qua_image5']) ? $images_data['qua_image5'] : '',
                'qua_image6' => !empty($images_data['qua_image6']) ? $images_data['qua_image6'] : '',
            ];

            $res = Db::table('cy_bis_images')->insert($insert_data);
        }else{
            //更新操作
            if(!empty($images_data)){
                $res =  Db::table('cy_bis_images')->where('id = '.$img_res['id'])->update($images_data);
            }
        }

        $this->success("更新成功!");
    }

    public function execUrl($address){
        $url = "http://restapi.amap.com/v3/place/text?key=4c9ea4c4b4f719f7e69d625586f8c00d&keywords=".$address."&types=&city=&children=1&offset=20&page=1&extensions=all";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    //获取省级信息
    public function getProvinceInfo($provinceId){
        $res = Db::table('store_province')->where("id = '$provinceId'")->find();
        return $res['p_name'];
    }

    //获取市级信息
    public function getCityInfo($cityId){
        $res = Db::table('store_city')->where("id = '$cityId'")->find();
        return $res['c_name'];
    }

    //店铺余额
    public function my_balance(){
        $bis_id = session('bis_id','','bis');
        $res = Db::table('cy_bis')->where('id = '.$bis_id)->find();

        return $this->fetch('',[
            'bis_res'  => $res
        ]);
    }
}
