<?php
namespace app\index\controller;
use app\index\model\Order;
use app\index\model\User;
use app\index\model\Contract;
use app\api\controller\Common;

class Member extends Common{
    //我的账户信息
    public function memberInfo(){
        $user = new User();
        $fields = [
            'phone','addTime'
        ];
        $where = [
            'uid'=>session('indexUserId')
        ];
        $res = $user->field($fields)->where($where)->find();
        $data = [
            'title'=>'账户信息'
        ];
        if($res){
            $data['userInfo'] = $res;
        }else{
            $data['userInfo'] = '';
        }
        $this->assign($data);
        return $this->fetch('index/member-info');
    }
    //修改账户密码
    public function changePwd(){
        if(request()->isAjax()){
            $input = input();
            $validate = new \app\common\validate\From();
            $result = $validate->scene('changePwd')->check($input);
            if(!$result){
                $this->error($validate->getError());
            }
            $uid = session('indexUserId');
            //加密盐
            $salt = Common::getSort();
            $password = md5(md5(trim($input['password'])).trim($salt));
            $data = [
                'salt'=>$salt,
                'password'=>$password
            ];
            $user = new User;
            $res = $user->where('uid', $uid)
                ->update($data);
            if($res){
                $this->success('修改成功！','','');
            }else{
                $this->error('修改失败！');
            }
        }
    }
    //更换手机号
    public function changePhone(){
        if(request()->isAjax()){
            $input = input();
            $validate = new \app\common\validate\From();
            $result = $validate->scene('changePhone')->check($input);
            if(!$result){
                $this->error($validate->getError());
            }
            if($input['notecaptcha'] != session('noteCode')){
                $this->error('短信验证码错误！');
            }
            $uid = session('indexUserId');
            $data = [
                'phone'=>$input['phone']
            ];
            $user = new User;
            $res = $user->where('uid', $uid)
                ->update($data);
            if($res){
                session('noteCode',null);
                $this->success('修改成功！','','');
            }else{
                $this->error('修改失败！');
            }

        }
    }
    // 我的钱包
    public function mywallet(){
        $member = new User();
        $field = [
            'balance',
            'btc_balance',
            'ltc_balance',
            'bch_balance'
        ];
        $uid = session('indexUserId');
        $result = $member->field($field)->where('uid',$uid)->find();
        $data = [
            'title'=>'我的钱包',
            'wallet' => $result
        ];
        
        $this->assign($data);
        return $this->fetch('index/mywallet');
    }
    // 币种转换
    public function currency_replace(){
        if(request()->isAjax()){
            $type = input('type'); // 取现币种
            $member = new User();
            $field = code_replace($type);
            $uid = session('indexUserId');
            $result = $member->field($field)->where('uid',$uid)->find();
            if($result){
                $data = [
                    'code' => 1,
                    'msg' => $result[$field]
                ];
                return $data;
            }
        }
    }
    // 余额提现
    public function putforward(){
        if(request()->isAjax()){
            $member = new User();
            $uid = session('indexUserId');
            $input = input();
            $money = $input['money']; // 取现金额
            $type = $input['type']; // 取现币种
            $field = code_replace($type);
            $current_balance = $member->where('uid',$uid)->value($field); // 取出该币种余额
            $new_balance = $current_balance - $money; // 取现后余额
            
            
            $data = [
                'code' => 1,
                'msg' => '您的提现申请已提交！'
            ];
            return $data;
        }
    }
    //订单列表
    public function myOrder(){
        if(request()->isAjax()){
            $uid = session('indexUserId');
            $order = new Order();
            $limit = !empty(input('limit'))?input('limit'):10;
            $fields = [
                'order_id','order_price',
                'deliveryMethod','shippingAddress',
                'order_status','order_addTime',
                'order_payTime','order_detailInfo',
                'order_describe','lengthTime'
            ];
            $res = $order->field($fields)->where('uid',$uid)->paginate($limit);
            if($res){
               $res = json_decode(json_encode($res),true);
                $data = [
                    'code'=>1,
                    'data'=>$res['data'],
                    'msg'=>'',
                    'count'=>$res['total']
                ];
                return $data;
            }else{
                $this->error('订单获取错误！');
            }
        }
        $data = [
            'title'=>'我的订单'
        ];
        $this->assign($data);
        return $this->fetch('/index/my-order');
    }
    //个人中心订单支付
    public function payOrder($oid){
        if(request()->isAjax()){
            $order = new Order();
            $where = [
                'order_id'=>$oid,
                'uid'=>session('indexUserId'),
                'order_status'=>0
            ];
            $res = $order->where($where)->find();
            if($res){
                $orderid = [
                    'order_id'=>$oid
                ];
                $encrypt = new \encrypt\Encrypt(config('ENCRYPT_KEY'));
                $token = $encrypt->safe_b64encode(json_encode($orderid));
                $this->success('正在跳转...','/orderBuy/'.$token);
            }else{
                $this->error('订单参数错误！');
            }
        }
    }
    //删除订单
    public function delOrder($oid){
        if(request()->isAjax()){
            $order = new Order();
            $where = [
                'order_id'=>$oid,
                'uid'=>session('indexUserId'),
                'order_status'=>1
            ];
            $res = $order->where($where)->find();
            if($res){
                $result = $order->where($where)->delete();
                if($result){
                    $this->success('删除成功！');
                }else{
                    $this->error('删除失败！');
                }

            }else{
                $this->error('订单信息错误！');
            }
        }
    }
    // 我的合约
    public function myContract(){
        if(request()->isAjax()){
            $contract = new Contract();
            $field = [
                'id','contract_name'
            ];
            $result = $contract->field($field)->paginate(50);
            if($result){
                $result = json_decode(json_encode($result),true);
                $data = [
                    'code'=>1,
                    'data'=>$result['data'],
                    'msg'=>'',
                    'count'=>$result['total']
                ];
                return $data;
            }
        }
        $data = [
            'title' => '我的合约'
        ];
        $this->assign($data);
        return $this->fetch('index/my-contract');
    }
    public function Contract_detail(){
        return $this->fetch('index/contract_detail');
    }


}