<?php
namespace app\index\controller;
use app\index\model\Order;

class OrderBuy extends Common{
    //订单支付页面
    public function orderBuy($orderid){
        $data = [
            'title'=>'订单支付',
        ];
        $order = new Order();
        $encrypt = new \encrypt\Encrypt(config('ENCRYPT_KEY'));
        $token = $encrypt->safe_b64decode($orderid);
        $res = json_decode($token,true);
        $oid = $res['order_id'];
        $where = [
            'order_id'=>$oid
        ];

        $fields = [
            'order_id','order_price',
            'deliveryMethod','shippingAddress',
            'order_detailInfo','order_describe'
        ];
        $res = $order->field($fields)->where($where)->find();
        if($res){
            $data['orderInfo'] = $res;
        }else{
            $this->error('订单信息出错！');
        }
        $this->assign($data);
        return $this->fetch('index/order-buy');
    }
}