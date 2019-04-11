<?php
namespace app\bis\controller;
use think\Controller;
class Index extends Base {

    //系统首页
    public function index(){
        return $this->fetch();
    }

    public function welcome(){
        return $this->fetch();
//        return '<marquee style="margin-top:20%; font-size: 40px;color: rgba(255, 17, 22, 0.2);"><strong>欢迎来到o2o主后台首页!</strong></marquee>';
    }

    public function test(){
        echo session('bis_id','','bis');
//        echo config('app_namespace');
    }
}
