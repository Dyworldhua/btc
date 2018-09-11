<?php
namespace app\index\controller;
use app\index\model\Carts;
use app\index\model\Mills;
use app\index\model\Order;
use app\index\model\User;
use app\index\model\ShippingAddress;
use app\index\model\DeliveryMethod;
use app\index\model\MillsTime;
use think\Db;

class ShopCart extends Common{
    //我的购物车
    public function myCarts(){
        $cart = new Carts();
        $fields = [
            'm.mill_price','m.mill_name',
            'c.order_num','m.mid','c.id',
            'c.lengthTime','m.mill_power',
            'm.mill_buyVal'
        ];
        $where = [
            'uid'=>session('indexUserId'),
            'order_status'=>0
        ];
        //获取我的购物车列表
        $result = $cart->alias('c')
            ->field($fields)
            ->where($where)
            ->join('mill m','m.mid = c.mid')
            ->select();
        $data = [
            'title'=>'我的购物车'
        ];
        if($result){
            $data['myCarts'] = $result;
        }else{
            $data['myCarts'] = '';
        }
        // 获取购买时长 -5.31 LCH
        $millstime = new MillsTime();
        $filed = [
            'id','mouth'
        ];
        $lengthtime = $millstime->field($filed)->select();
        if($lengthtime){
            $data['lengthtime'] = $lengthtime; 
        }
        //获取我的配送地址
        $address = new ShippingAddress();
        $where = [
            'uid'=>session('indexUserId')
        ];
        $fields = [
            'id',
            'address',
            'linkName',
            'linkPhone',
            'postcode'
        ];
        $res = $address->field($fields)->where($where)->select();
        if($res){
            $data['address']=$res;
        }else{
            $data['address']='';
        }
        //获取支持的配送方式
        $deliverymethod = new DeliveryMethod();
        $fields = [
            'id',
            'methodName'
        ];
        $res = $deliverymethod->field($fields)->where('status',1)->select();
        if($res){
            $data['methods'] = $res;
        }else{
            $data['methods'] = '';
        }
        $this->assign($data);
        return $this->fetch('index/my-carts');
    }
    //加入购物车
    public function addCart(){
        if(request()->isAjax()){
            $input = input();
            // var_dump($input);exit;
            $milltime = $input['lengthtime']; // 获取购买时长
            $mid = $input['mid']; // 获取产品ID
            $num = $input['num']; // 获取产品数量
            if(!is_numeric($num) || floor($num) != $num){  // is_numeric 判断参数是否为数字或数字字符串
                $this->error('数量类型错误！'); // 判断购买数量类型是否正确
            }
            $mill = new Mills();
            $where = [
                'mid'=>$mid 
            ];
            $res = $mill->field('mill_price,mill_buyVal')->where($where)->find(); // 取出当前产品的价格和最大购买数量

            if(!$res){
                $this->error('数据错误！'); 
            }else{
                if($num > $res['mill_buyVal']){
                    $this->error('最大购买数量为：'.$res['mill_buyVal']); // 判断购买数量是否超过最大数量
                }
            }
            $user = new User();
            $fields = [
                'status',
                'uid' 
            ];
            $result = $user->field($fields)->where(['uid'=>session('indexUserId')])->find(); // 判断用户是否正常登陆

            if($result){
                if($result['status'] != 1){
                    $this->error('用户状态异常！','#');
                }
            }else{
                $this->error('登录状态异常，请重新登录！','/login');
            }

            $cart = new Carts();
            $where = [
                'mid'=>$mid,
                'uid'=>session('indexUserId'),
                'order_status'=>0
            ];
            $result = $cart->where($where)->find();
            if($result){
                $res = $cart->where($where)->setInc('order_num',$num);
            }else{
                $data = [
                    'order_num'=>$num,
                    'mid'=>$mid,
                    'order_addTime'=>time(),
                    'uid'=>session('indexUserId'),
                    'lengthTime'=>$milltime
                ];
                $cart->data($data);

                $res = $cart->save();
            }

            if($res){
                $this->success('添加成功！','/myCarts');
            }else{
                $this->error('添加失败！','#');
            }
            return $input;
        }
    }
    //收费明细
    public function chargeDetail($mid){
            $cart = new Carts();
            $pro_id['c.mid'] = array("in",$mid);  // 勾选产品ID
            $field = [
                'c.order_num',
                'c.lengthTime',
                'm.mill_name',
                'm.mill_price',
                'm.mill_power'
            ];
            $result = $cart
            ->alias('c')
            ->field($field)
            ->where($pro_id)
            ->where('uid',session('indexUserId'))
            ->join('btc_mill m','m.mid = c.mid')
            ->select();
            $data['charge'] = $result;
            $this->assign($data);
            return $this->fetch('index/charge-detail');
    }
    //修改订单数据
    public function editOrder(){

    }
    //删除订单
    public function delOrder($id){
        if(request()->isAjax()){
            $mid = $id;
            $cart = new Carts();
            $res = $cart->where('mid',$mid)->delete();
            if($res){
                $msg = [
                    'code'=>1,
                    'msg'=>'删除成功！',
                ];
                return $msg;
            }else{
                $msg = [
                    'code'=>0,
                    'msg'=>'删除失败！',
                ];
                return $msg;
            }
        }
    }
    //提交订单
    public function addOrder(){
        if(request()->isAjax()){
            $input = input();
            // var_dump($input);exit;
            if(empty($input['agreement'])){
                $this->error('请同意购买协议！');
            }
            if(empty($input['address'])){
                $this->error('请选择配送地址！');
            }
            $aid = $input['address']; 
            $methodId = $input['method'];
            $lengthtime = $input['length']; // 购买时长
            $addres = new ShippingAddress();
            $fields = [
                'address','linkName',
                'linkPhone','postcode'
            ];
            $res = $addres->field($fields)->where('id',$aid)->find();
            if($res){
                $shipping = json_decode($res['address'],true);
                array_push($shipping,$res['linkName'],$res['linkPhone'],$res['postcode']);
                //配送地址信息
                $shippingAddress = json_encode($shipping);
            }else{
                $this->error('参数错误！');
            }
            $DeliveryMethod = new DeliveryMethod();
            $fields = [
                'methodName'
            ];
            $res = $DeliveryMethod->field($fields)->where('id',$methodId)->find();
            if($res){
                //配送方式
                $deliveryMethod = $res['methodName'];
            }else{
                $this->error('参数错误！');
            }
            $mids = implode(',',$input['pro']);
            $cart = new Carts();
            $fields = [
                'c.order_num',
                'm.mill_name',
                'm.mill_price',
                'c.lengthTime',
                'm.mill_power'
            ];
            
            $res = $cart->alias('c')
                ->field($fields)
                ->where('c.mid','in',"{$mids}")
                ->where('c.uid',session('indexUserId'))
                ->join('btc_mill m','c.mid = m.mid')
                ->select();
            if($res){
                //订单详细信息
                $order_detailInfo = json_encode($res);
                //订单金额
                $order_price = '';
                $prices = json_decode(json_encode($res),true);
                foreach($prices as $price){
                    $order_price += ($price['order_num']*$price['mill_price'])+($price['lengthTime']*3*30*24*$price['mill_power']);
                }

            }else{
                $this->error('参数错误！');
            }

            //订单描述
            $order_describe = !empty($input['orderDescribe'])?$input['orderDescribe']:'';
            //用户id
            $uid = session('indexUserId');
            //添加时间
            $order_addtime = time();
            //订单号
            $order_id = self::getOrderId();

            $data = [
                'order_id'       => $order_id,
                'order_price'    => $order_price,
                'deliveryMethod' => $deliveryMethod,
                'shippingAddress'=> $shippingAddress,
                'order_addTime'  => $order_addtime,
                'uid'            => $uid,
                'order_detailInfo'=> $order_detailInfo,
                'order_describe'  => $order_describe,
                'order_payTime' => 0,
                'lengthTime' => $lengthtime
            ];
            $order = new Order();
            //添加订单
            $order->data($data);
            $result = $order->save();
            if($result){
                //删除购物车表信息
                $data = [
                    'order_status'=>1
                ];
                $cart->where('mid','in',"{$mids}")
                    ->where('uid',$uid)
                    ->update($data);
                $cart->where('mid','in',"{$mids}")->delete();
                $encrypt = new \encrypt\Encrypt(config('ENCRYPT_KEY'));
                $orderInfo = json_encode(['order_id'=>$order_id]);
                $token = $encrypt->safe_b64encode($orderInfo);
                $this->success('订单提交成功！','/orderBuy/'.$token);


            }else{
                $this->error('订单提交失败！');
            }
        }


    }
    //添加配送地址
    public function addAddress(){
        if(request()->isAjax()){
            $input = input();
            $validate = new \app\common\validate\From();
            $result = $validate ->scene('addAddress')->check($input);
            if(!$result){
                $this->error($validate->getError());
            }
            $addressed = [
                $input['country'],
                $input['provid'],
                $input['cityid'],
                $input['areaid'],
                $input['detail_info'],
            ];
            $address = json_encode($addressed);
            $name = $input['name'];
            $phone = $input['phone'];
            $postcode = $input['postcode'];
            $data = [
                'linkName' => $name,
                'linkPhone' => $phone,
                'address' => $address,
                'postcode' => $postcode,
                'uid' => session('indexUserId'),
                'addTime'=>time()
            ];
            $ShippingAddress = new ShippingAddress();
            $ShippingAddress->data($data);
            $result = $ShippingAddress->save();
            if($result){
                $id = $ShippingAddress->getLastInsID();
                $this->success('添加成功！','',$id);
            }else{
                $this->error('添加失败！');
            }

        }
        return $this->fetch('index/add-address');
    }
    //生成订单号
    static public function getOrderId(){
        $order = new Order();
        $order_id = date('YmdHis').rand(100000,999999);
        $res = $order->where('order_id')->where(['order_id'=>$order_id])->find();
        if($res){
            self::getOrderId();
        }
        return $order_id;
    }

    public function cartProEdit(){
        if(request()->isAjax()){
            $cart = new Carts();
            if(!empty($_POST['second_num'])){
                $pro_num = $_POST['second_num'];
                $id = $_POST['pid'];
                $update = $cart
                ->where('id',$id)
                ->where('uid',session('indexUserId'))
                ->update(['order_num'=>$pro_num]);
                exit;
            }
            if(!empty($_POST['new_length'])){
                $new_length = $_POST['new_length'];
                $id = $_POST['pid'];
                $update = $cart
                ->where('id',$id)
                ->where('uid',session('indexUserId'))
                ->update(['lengthTime'=>$new_length]);
                exit;
            }

            $pro_num = $_POST['num'];
            $length = $_POST['length'];
            $pro_id = $_POST['id'];
            $update = $cart
            ->where('id',$pro_id)
            ->where('uid',session('indexUserId'))
            ->update(['order_num'=>$pro_num,'lengthTime'=>$length]);
        }
    }
    
}