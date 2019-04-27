<?php
namespace app\bis\controller;
use think\Controller;
use think\Db;

class Members extends Base {

    //用户列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $id = input('get.id');
        $truename = input('get.truename');
        $mobile = input('get.mobile');
        $mem_id = input('get.mem_id');
        $nickname = input('get.nickname');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $count = model('Members')->getAllMembersCount($bis_id);
        $pages = ceil($count / $limit);
        $res = model('Members')->getAllMembers($bis_id,$limit,$offset,$id,$truename,$mobile,$mem_id,$nickname);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'id'  => $id,
            'truename'  => $truename, 
            'mobile'  => $mobile,
            'mem_id'  => $mem_id,
            'nickname'  => $nickname,
        ]);
    }

    //详情页面
    public function detail(){
        //获取数据
        $id = input('get.mem_id');
        //设置查询条件
        $where['m.id'] = $id;
        $res = Db::table('cy_members')->alias('m')->field('')
                ->join('store_province p','m.province_id = p.id','LEFT')
                ->join('store_city c','m.city_id = c.id','LEFT')
                ->where($where)
                ->find();
        return $this->fetch('',[
            'res'   =>  $res
        ]);
    }

     public function edit()
    {
        $id = input('get.id');

        $res = Db::table('cy_members')->where(['id'=>$id])->find();

        $this->assign('res',$res);
        return $this->fetch();
    }

    //修改会员数据
    public function editSave()
    {
        //获取参数
        $id = input('post.id');

        $param = input('post.');

        //设置添加到数据库的数据


        $data = [
            'truename' => $param['truename'],
            'address' => $param['address'],
            'email' => $param['email'],
            'mobile' => $param['mobile'],
            'dzp_ci' => $param['dzp_ci'],
            'jifen' => $param['jifen'],
            'balance' => $param['balance'],
            'stop_time' => $param['stop_time'],
            'last_login_time' => date('Y-m-d H:i:s', time())
        ];

        $where = ['id'=>$id];
        $re = Db::table('cy_members')->where($where)->update($data);

        if ($re)
        {
            return redirect('bis/Members/index');
        }else{
            $this->error('修改失败');
        }
    }

    public function remove()
    {
        $id = input('get.id');
//        $res = Db::table('cy_members')->where(['id'=>$id])->update(['status'=>-1]);
        $res = Db::table('cy_members')->where(['id'=>$id])->delete();
        if ($res)
        {
            return redirect('members/index');
        }else{
            $this->error('删除数据失败');
        }
    }




}
