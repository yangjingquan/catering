<?php
namespace app\api\controller;
use think\Controller;
class City extends Controller{

    private $obj;
    public function _initialize(){
        $this->obj = model('City');
    }

    //根据父id获取市级信息
    public function getCitysByParentId(){
        //获取参数
        $parent_id = input('post.parent_id');
        $res = model('City')->getCitysByParentId($parent_id);

        if($res){
            return show(1,'success',$res);
        }else{
            return show(0,'error');
        }
    }
}
