<?php
namespace app\common\validate;
use think\Validate;
class Banner extends Validate{

    //定义规则
    protected $rule = [
//        ['redirect_url','require', '跳转url必须填写'],
        ['res_id','number', '推荐位id必须存在'],

    ];


    //场景设置
    protected $scene = [
//        'add'  =>  ['redirect_url'],//添加
        'update'   => ['res_id','type'],//修改
    ];

}
?>