<?php
namespace app\index\controller;
use think\Controller;
class Test extends Controller{
    public function test(){

         $encrypt = new \encrypt\Encrypt('KDAKAm0lp15XcORJ');
        $orderInfo = json_encode(['order_id'=>'20180109172120870553']);
        $token = $encrypt->safe_b64encode($orderInfo);
        $tokens = $encrypt->safe_b64decode('eyJvcmRlcl9pZCI6IjIwMTgwMTA5MTcyMTIwODcwNTUzIn0');
        print_r($token);
        print_r('<br/>');
        print_r($tokens);
    }
}