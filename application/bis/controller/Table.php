<?php
namespace app\bis\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Table extends Base {

    //桌位列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Table')->getTablesCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Table')->getTables($bis_id,$limit,$offset);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }

    //添加桌位页面
    public function add(){
        return $this->fetch('');
    }


    //编辑桌位
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该桌位信息
        $table = Db::table('cy_tables')->where('id = '.$id)->find();

        return $this->fetch('',[
            'table'  => $table
        ]);
    }

    //添加桌位
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        $bis_id = session('bis_id','','bis');
        if(empty($bis_id)){
            $this->error('店铺id不存在,请重新登录');
        }
        Db::startTrans();
        try{
            //设置添加到数据库的数据
            $data = [
                'bis_id' => $bis_id,
                'table_name' => $param['table_name'],
                'create_time' => date('Y-m-d H:i:s')
            ];

            $tableId = model('Table')->add($data);

            //设置二维码
            $this->setTableAcode($bis_id,$tableId);

            // 提交事务
            Db::commit();
        }catch(\Exception $e){
            // 回滚事务
            Db::rollback();
            $this->error('新增失败');
        }
        $this->success("新增成功");
    }

    //更改状态
    public function updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('cy_tables')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //更改桌位状态
    public function updateTableStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.show');
        $data['shows'] = $status;
        $res = Db::table('cy_tables')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改桌位
    public function updateTable(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        Db::startTrans();
        try{
            $acodeUrl = $this->setTableAcode($param['bis_id'],$param['id']);

            //设置添加到数据库的数据
            $data = [
                'table_name' => $param['table_name'],
                'acode' => $acodeUrl,
            ];

            Db::table('cy_tables')->where('id = '.$param['id'])->update($data);
            //提交事务
            Db::commit();
        }catch(\Exception $e){
            // 回滚事务
            Db::rollback();
            $this->error('修改失败');
        }
        $this->success("修改成功");
    }

    //设置桌位二维码
    public function setTableAcode($bis_id,$table_id){
        $homeInfo = Db::table('store_home_info')->field('appid,secret')->where('id = 1')->find();
        $appid = $homeInfo['appid'];
        $secret = $homeInfo['secret'];

        if($appid == ''){
            return show(0,'fail','请先设置appid');
        }
        if($secret == ''){
            return show(0,'fail','请先设置secret');
        }

        //创建文件夹
        $upload_file_path = 'table/acode/';
        if(!is_dir($upload_file_path)) {
            mkdir($upload_file_path,0777,true);
        }

        //获取access_token
        $access_token_json = $this->getAccessToken($appid,$secret);
        $arr = json_decode($access_token_json,true);
        $access_token = $arr['access_token'];

        //设置路径及二维码大小
        $path="pages/diancan/diancan?table_id=".$table_id."&bis_id=".$bis_id;
        $width=430;

        $post_data='{"path":"'.$path.'","width":'.$width.'}';
        $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=".$access_token;

        $result = $this->api_notice_increment($url,$post_data);
        //设置图片名称
        $img_name = md5('tb_'.time().$bis_id.$table_id).'.png';

        //设置图片路径
        $img_path = $upload_file_path.$img_name;

        file_put_contents($img_path, $result);
        //将图片路径更新到商家表中
        $up_data['acode'] = self::IMG_URL.$img_path;
        $res = Db::table('cy_tables')->where('id = '.$table_id)->update($up_data);
        if($res > 0){
            return $up_data['acode'];
        }else{
            return false;
        }
    }

    //获取access_token
    public function getAccessToken($appid,$secret){

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    function api_notice_increment($url, $data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }else{
            return $tmpInfo;
        }
    }

}
