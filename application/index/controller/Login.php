<?php
namespace app\index\controller;
use think\Controller;
use app\api\controller\SendCode;
use app\index\model\User;
use app\api\controller\Common;
class Login extends Controller{
    //会员登录
    public function login(){
        if(request()->isAjax()){
            $input = input();
            $validate = new \app\common\validate\From();
            $result = $validate->scene('index_login')->check($input);
            if(!$result){
                 $this->error($validate->getError());
            }
            $username = trim($input['username']);
            $password = md5(trim($input['password']));
            $user = new User();
            $fields = [
                'password','salt',
                'status','uid',
                'lastLoginTime',
                'loginIp'
            ];
            $res = $user->where('username', $username)->field($fields)->find();

            if($res){
                if(!$res['status']){
                    $this->error('账号状态异常，禁止登陆！');
                }
                $pwd = md5($password.$res['salt']);
                if($pwd != $res['password']){
                    $this->error('密码错误！');
                }else{
                    session('indexUser',$username);
                    session('indexUserId',$res['uid']);
                    session('lastLoginTime',$res['lastLoginTime']);
                    session('lastLoginIp',$res['loginIp']);
                    $data = [
                        'lastLoginTime'=>time(),
                        'loginIp'=>Common::getIp()
                    ];
                    $where = [
                        'username'=>$username
                    ];
                    $user->save($data,$where);
                    $this->success('登陆成功！','/member/member_info');
                }
            }else{
                $this->error('用户名不存在！');
            }
            return ;
        }
        return $this->fetch('/index/login');
    }
    //注册接口
    public function register(){
        if(request()->isAjax()){
            $input = input();
            if(empty($input['agreement'])){
                $this->error('请先阅读条款！');
            }
            $phone = trim($input['phone']);
            //检查手机号是否已经注册
            $res = User::get(['phone' => $phone]);
            if($res){
                $this->error('手机号已经注册！');
            }
            if($input['notecaptcha'] != session('noteCode')){
                $msg = [
                    'code'=>0,
                    'msg'=>'短信验证码错误！'
                ];
                return $msg;
            }
            $validate = new \app\common\validate\From();
            $result = $validate->scene('register')->check($input);
            if(!$result){
                $this->error($validate->getError());
            }

            $username = trim($input['username']);
            //加密盐
            $sort = Common::getSort();
            $password = md5(md5(trim($input['password'])).trim($sort));

            //检查用户名是否存在
            $user = User::get(['username' => $username]);
            if($user){
                $this->error('用户名已存在！');
            }
            //推荐码
            $orangeKey = Common::getSort(20);
            $data = [
                'username'=>$username,
                'password'=>$password,
                'salt'=>$sort,
                'phone'=>$phone,
                'addTime'=>time(),
                'orangeKey'=>$orangeKey,
                'status'=>'1'
            ];
            //插入数据库
            $user = new User();
            $user->data($data);
            $result = $user->save();
            if($result){
                session('noteCode',null);
               $this->success('注册成功,请登录！','/login');
            }else{
                $this->error('注册失败，请重试！');
            }
            return;

        }
        return $this->fetch('index/register');
    }
    //退出登录
    public function logout(){
        session('indexUser',null);
        session('indexUserId',null);
        $this->redirect('/');
    }
    //获取短信验证码
    public function getNoteCode(){
        $input = input();
        $this->error('11111');
        $phone = $input['phone'];
        $validate = new \app\common\validate\From();
        $result = $validate->scene('getNoteCode')->check($input);
        if(!$result){
            $this->error($validate->getError());
        }

        $send = new SendCode();
        $res = $send->sendCode($phone);
        return $res;
    }


}