<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\User;
class Login extends Controller{
    public function login(){
        if(request()->isAjax()){
            $input = input();
            $validate = new \app\common\validate\From();
            $result = $validate->scene('admin_login')->check($input);
            if(!$result){
                $this->error($validate->getError());
            }
            $username = trim($input['username']);
            $user = new User();
            $fields = ['salt','password','status'];
            $result = $user->field($fields)->where(['username'=>$username])->find();
            if($result){
                if($result['status'] == 0){
                    $this->error('账号状态异常');
                }
                $sort = $result['salt'];
                $password = md5(md5(trim($input['password'])).$sort);
                if($result['password'] != $password){
                    $this->error('密码错误！');
                }else{
                    session('adminUser',$username);

                    $this->success('登录成功！','/admin');
                }
            }else{
                $this->error('用户不存在！');
            }
            return;
        }
        return $this->fetch('login/login');
    }
    //退出登录
    public function logout(){
       session('adminUser',null);
        $this->redirect('/admin/login');
    }

}