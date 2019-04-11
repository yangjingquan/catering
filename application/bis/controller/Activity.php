<?php
namespace app\bis\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Activity extends Base {

    //活动列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Activitys')->getActivitysCount($bis_id);

        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Activitys')->getActivitys($bis_id,$limit,$offset);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }

    //添加活动页面
    public function add(){
        return $this->fetch('');
    }

    //编辑活动
    public function edit(){
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id','','bis');
        //获取该分类信息
        $res = Db::table('cy_activitys')->where('id = '.$id)->find();

        return $this->fetch('',[
            'activity'  => $res
        ]);
    }

    //添加活动
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => session('bis_id','','bis'),
            'type' => $param['act_type'],
            'activity_name' => $param['activity_name'],
            'max' => $param['max'],
            'lose' => $param['lose'],
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        $res = model('Activitys')->add($data);

        if($res){
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
        $res = Db::table('cy_activitys')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改活动
    public function updateActivity(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        $param = input('post.');
        //设置添加到数据库的数据
        $data = [
            'activity_name' => $param['activity_name'],
            'type' => $param['act_type'],
            'max' => $param['max'],
            'lose' => $param['lose'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_activitys')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }
}
