<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Db;

class Reserve extends Base {
    const PAGE_SIZE = 20;
    //店铺信息
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Reserve')->getAllCount($bis_id);

        //总页码
        $pages = ceil($count / $limit);
        //获取店铺信息
        $bis_res = model('Reserve')->getAllInfo($bis_id,$limit,$offset);

        return $this->fetch('',[
            'res'  => $bis_res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }

    //添加类型页面
    public function add(){
        return $this->fetch('');
    }

    //添加类型
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => session('bis_id','','bis'),
            'table_name' => $param['table_name'],
            'deposit' => $param['deposit'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $res = model('Reserve')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //编辑
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该分类信息
        $res = Db::table('cy_reserve_table_info')->where('id = '.$id)->find();

        return $this->fetch('',[
            'res'  => $res
        ]);
    }

    //删除
    public function remove(){
        //获取参数
        $id = input('get.id');
        $data['status'] = -1;
        $res = Db::table('cy_reserve_table_info')->where('id = '.$id)->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    //修改活动
    public function update(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        $param = input('post.');
        //设置添加到数据库的数据
        $data = [
            'table_name' => $param['table_name'],
            'deposit' => $param['deposit'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_reserve_table_info')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

}
