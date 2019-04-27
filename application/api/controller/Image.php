<?php
namespace app\api\controller;
use think\Controller;
use think\File;
use think\Request;
class Image extends Controller{

    //上传文件(多个文件)
    public function uploadM($filename, $type){
        // 获取表单上传文件 例如上传了001.jpg

        $files = request()->file($filename);
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads', $type);
            if($info){
                $data[] =  'uploads'. DS .$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        return $data;

    }


    //上传文件(单个文件)
    public function uploadS($filename, $type){
        // 获取表单上传文件 例如上传了001.jpg

        $file = request()->file($filename);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads', $type);
        if($info){
            return 'uploads'. DS .$info->getSaveName();
        }else{
            // 上传失败获取错误信息
            return $file->getError();
        }
    }
}
