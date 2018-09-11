<?php
/**
 * Created by PhpStorm.
 * User: Udea_PHP2
 * Date: 2018/1/2
 * Time: 14:23
 */
namespace app\api\controller;
use think\Controller;
use think\Loader;

class SendCode extends Controller{
    //发送验证码接口
    public function sendCode($phone = ''){
        //加载发送短信api
//        Loader::import('sendsms.SendSms', EXTEND_PATH);
        //这里调用第三方发送短信验证码接口
        if(!$phone){
            $msg = [
                'code'=>0,
                'msg'=>'请填写手机号'
            ];
            return $msg;
        }
        $code = mt_rand(1000,9999);

        $data = [
            'phone'=>$phone,
            'code'=>$code
        ];
        $sms = new \sendsms\sendSms();
        $res = $sms->send($data);

        if(session('?beforeSend') && (session('beforeSend')+60*2) > time()){
            $time = (session('beforeSend')+60*2)-time();
            $msg = [
                'code'=>0,
                'msg'=>"请在".$time."s后再获取短信验证码！",
                'data'=>''
            ];
            return $msg;
        }
        if($res->Code == 'OK'){
            session('noteCode',$code);
            session('beforeSend',time());
            $msg = [
                'code'=>1,
                'msg'=>'短信发送成功！',
                'data'=>''
            ];
            return $msg;
        }else{
            $msg = [
                'code'=>400,
                'msg'=>'短信发送失败，请稍后重试！',
                'data'=>$res->Message
            ];
            return $msg;
        }

    }
}