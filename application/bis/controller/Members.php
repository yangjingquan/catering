<?php
namespace app\bis\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Members extends Base {
    const PAGE_SIZE = 20;

    //餐饮会员列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        $count = model('Members')->getAllMembersCount($bis_id);
        $pages = ceil($count / $limit);
        $res = model('Members')->getAllMembers($bis_id,$limit,$offset);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }
}
