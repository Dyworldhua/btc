<?php
namespace app\index\controller;
use app\index\model\ShippingAddress;

class Address extends Common{
    //配送地址
    public function shippingAddress(){
        $address = new ShippingAddress();
        $fields = [
            'id','address','linkName',
            'linkPhone','postcode'
        ];
        $where = [
            'uid'=>session('indexUserId')
        ];
        $res = $address->field($fields)->where($where)->order('addTime asc')->select();
        $data = [
            'title'=>'我的配送地址'
        ];
        if($res){
            $data['address']=$res;
        }else{
            $data['address']='';
        }

        $this->assign($data);
        return $this->fetch('index/shipping-address');
    }
    //编辑配送地址
    public function editAddress($id){
        $address = new ShippingAddress();
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
            $addresss = json_encode($addressed);
            $name = $input['name'];
            $phone = $input['phone'];
            $postcode = $input['postcode'];
            $id = $input['id']/989;
            $data = [
                'linkName'  => $name,
                'linkPhone' => $phone,
                'address'   => $addresss,
                'postcode'  => $postcode,
                'uid'       => session('indexUserId'),

            ];
            $result = $address->save($data,['id' => $id]);
            if($result){
                $this->success('修改成功！');
            }else{
                $this->error('修改失败！');
            }
        }
        $aid = $id/989;
        $fields = [
            'id','address','linkName',
            'linkPhone','postcode',
        ];
        $res = $address->field($fields)->where('id',$aid)->find();
        if($res){
            $info = [
                'id'       => $res['id']*989,
                'name'     => $res['linkName'],
                'phone'    => $res['linkPhone'],
                'postcode' => $res['postcode'],
                'address'  => json_decode($res['address'],true)
            ];
            $data = [
                'address'=>$info
            ];
            $this->assign($data);
            return $this->fetch('index/edit-address');
        }else{
            $this->error('数据错误！');
        }

    }
    //删除配送地址
    public function delAddress(){
        if(request()->isAjax()){
            $address = new ShippingAddress();
            $input = input();
            $id = $input['id']/989;
            $res = $address->where('id',$id)->delete();
            if($res){
                $this->success('删除成功！');
            }else{
                $this->error('删除失败！');
            }
        }
    }
}