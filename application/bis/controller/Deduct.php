<?php
namespace app\bis\controller;
use think\Controller;
use think\Exception;
use think\Validate;
use think\Db;

class Deduct extends Base {

    const PAGE_SIZE = 20;

    //充值记录
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Deduct')->getDeductCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Deduct')->getDeductInfo($bis_id,$limit,$offset);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
        ]);
    }

}
