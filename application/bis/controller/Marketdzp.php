<?php
namespace app\bis\controller;
use think\Controller;
use think\Db;

class Marketdzp extends Base {

    //获取奖品列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $count = model('Marketdzp')->getAllJiangpinCount($bis_id);
        $pages = ceil($count / $limit);
        $res = model('Marketdzp')->getAllJiangpin($bis_id,$limit,$offset);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
        ]);
    }

    //奖品设置列表
    public function jiangpin(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //获取总数
        $count = model('Marketdzp')->getAllJpCount($bis_id);
        $pages = ceil($count / $limit);
        $rec_res = model('Marketdzp')->getAllJp($bis_id,$limit,$offset);
        return $this->fetch('',[
            'rec_res'  => $rec_res,
            'pages'  => $pages,
            'current_page'  => $current_page,
        ]);
    }

    //添加奖品页面
    public function add(){
        return $this->fetch();
    }

    //奖品
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        
        $bis_id = session('bis_id','','bis');
        //设置添加到数据库的数据
        $data = [
            'bis_id'  => $bis_id,
            'jp_id' => $param['jp_id'],
            'jp_name' => $param['jp_name'],
            'jiaodu' => $param['jiaodu'],
            'status' => '1'
        ];

        $r_res = Db::table('cy_dzp_jiangpin')->insert($data);

        if($r_res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //编辑奖品页面
    public function edit(){
        //获取参数
        $id = input('get.id');
        $where = [
            'id'  => $id
        ];

        $rec_res = Db::table('cy_dzp_jiangpin')->where($where)->find();

        return $this->fetch('',[
            'rec_res'  => $rec_res
        ]);
    }

    //修改奖品页面信息
    public function update(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        
        //设置更新数据
        $data['jp_id'] = $param['jp_id'];
        $data['jp_name'] = $param['jp_name'];
        $data['jiaodu'] = $param['jiaodu'];

        //更新数据
        Db::table('cy_dzp_jiangpin')->where('id = '.$param['res_id'])->update($data);

        $this->success("修改成功!");
    }

    //删除奖品
    public function updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('cy_dzp_jiangpin')->where('id = '.$id)->update($data);

        if($res){
            //获取bis_id
            $bis_id = model('Recommend')->getBisIdById($id);
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }


}
