<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller{
    public function index(){
        return $this->fetch('index/index');
    }
    public function more($id){
        // echo "123";
        var_dump($_GET['id']);
    }
    public function testajax(){
        return $this->fetch('index/testajax');
    }
    public function returnajax(){ // request()->isAjax() 判断是否ajax异步
        if(request()->isAjax()){
            $input = input('value');
            // var_dump($input);die;
            $code = [
                'msg' => $input,
                'code' => 1
            ];
            return $code;
        }
    }
}
