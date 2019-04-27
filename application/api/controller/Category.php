<?php
namespace app\api\controller;
use think\Controller;
class Category extends Controller{

    private $obj;
    public function _initialize(){
        $this->obj = model('Category');
    }
    //首页信息
    public function getCategoryByParentId(){

        $category_id = input('post.category_id',0,'intval');

        if(!$category_id){
            return show(0,'ID不合法');
        }
        //通过id获取二级分类
        $categorys = $this->obj->getNormalCategoryByParentId($category_id);

        if(!$categorys){
            return show(0,'error');
        }
        return show(1,'success',$categorys);
    }

    //获取一级分类信息
    public function getNormalFirstCategory(){
        $first_category = model('Category')->getNormalFirstCategory();

        if($first_category){
            return show(1,'success',$first_category);
        }else{
            return show(0,'error');
        }
    }
}
