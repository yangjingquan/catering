<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
class Feedback extends Controller{

    //录入反馈信息
    public function addFeedbackInfo(){
        $param = input('post.');
        $username = $param['username'];
        $usertel = $param['usertel'];
        $usercontent = $param['usercontent'];

        $data = [
            'username'  => $username,
            'usertel'  => $usertel,
            'usercontent'  => $usercontent,
            'ip'  => $this->getClientIP(),
            'create_time'  => date('Y-m-d H:i:s'),
        ];

        $res = Db::table('store_feedback')->insert($data);

        if($res){
            return 1;
        }else{
            return 0;
        }
    }

    function getClientIP(){
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }

}
