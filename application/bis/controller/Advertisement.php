<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Db;

class Advertisement extends Base {

    const PAGE_SIZE = 10;

    public function index(){
        $current_page = input('get.current_page',1,'intval');
        $bis_id = session('bis_id','','bis');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Advertisement')->getAdsCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        $res = model('Advertisement')->getAds($bis_id,$limit,$offset);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }

    public function changeOrder(){
        $id = input('post.id');
        $order = input('post.order');

        $data = ['listorder'  => $order];

        $res = Db::table('cy_ads')->where("id = $id")->update($data);

        if($res){
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }else{
            return show(1,'error');
        }
    }

    public function add(){
        return $this->fetch('');
    }

    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => session('bis_id','','bis'),
            'jump_url' => $param['jump_url'],
            'created_time' => date('Y-m-d H:i:s'),
            'updated_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img_error = !empty($_FILES['img_url']) ? $_FILES['img_url']['error'] : -1;

        if($img_error == 0){
            $image_data = $image->uploadS('img_url','ads');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img_url'] = self::IMG_URL.$image_data;
        }

        $res = Db::table('cy_ads')->insert($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    public function edit(){
        $id = input('id');
        $res = model('Advertisement')->getAdInfoById($id);
        return $this->fetch('',[
            'res'  => $res,
            'no_img_url' => self::NO_IMG_URL
        ]);
    }

    public function update(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $id = $param['id'];
        //设置添加到数据库的数据
        $data = [
            'jump_url' => $param['jump_url'],
            'updated_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img_error = !empty($_FILES['img_url']) ? $_FILES['img_url']['error'] : -1;

        if($img_error == 0){
            $image_data = $image->uploadS('img_url','ads');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img_url'] = self::IMG_URL.$image_data;
        }

        $res = Db::table('cy_ads')->where("id = $id")->update($data);

        if($res){
            $this->success("更新成功");
        }else{
            $this->error('更新失败');
        }
    }

    public function remove(){
        //获取提交的数据
        $id = input('get.id');
        $data = ['status'  => -1];
        $res = Db::table('cy_ads')->where("id = $id")->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

}
