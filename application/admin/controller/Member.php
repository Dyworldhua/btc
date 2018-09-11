<?php
namespace app\admin\controller;
use app\admin\model\Members;
use app\index\model\User;
use app\api\controller\Common;
class Member extends Common{
    //会员列表
    public function member_list(){
        if(request()->isAjax()){
            $member = new Members();
            $fields = [
                'uid','username',
                'phone','btc_balance',
                'balance','status',
                'addTime','orangeKey',
                'lastLoginTime','loginIp'
            ];
            $limit = (null !== input('limit'))?input('limit'):20;
            $result = $member->field($fields)->paginate($limit);
            // dump($result);exit;

            if($result){
                $result = json_decode(json_encode($result));
                $data = [
                    'code'=>1,
                    'count'=>$result->total,
                    'msg'=>'',
                    'data'=>$result->data
                ];
                return $data;
            }else{
                $data = [
                    'code'=>0,
                    'count'=>0,
                    'msg'=>'数据获取失败！',
                    'data'=>[]
                ];
                return $data;
            }

        }

        return $this->fetch('member/member-list');
    }
    //添加会员
    public function member_add(){
        if(request()->isAjax()){
            $input = input();

            $validate = new \app\common\validate\From();
            $result = $validate->scene('member_add')->check($input);
            if(!$result){
                $this->error($validate->getError());
            }
            $username = trim($input['username']);
            $phone = trim($input['phone']);
            $user = new User();
            $userRes = $user->where('username',$username)->find();
            if($userRes){
                $this->error('用户名已存在！');
            }
            $phoneRes = $user->where('phone',$phone)->find();
            if($phoneRes){
                $this->error('手机号已存在！');
            }
            $salt = Common::getSort();
            //推荐码
            $orangeKey = Common::getSort(20);
            $password = md5(md5(trim($input['password'])).$salt);

            $balance = trim($input['balance']);
            $btc_balance = trim($input['btc_balance']);
            $status = isset($input['status'])?1:0;
            $data = [
                'username'  => $username,
                'password'  => $password,
                'phone'     => $phone,
                'balance'   => $balance,
                'btc_balance' => $btc_balance,
                'orangeKey' => $orangeKey,
                'status'    => $status,
                'salt'      => $salt,
                'addTime'   => time(),
                
            ];
            $user->data($data);
            $result = $user->save();
            if($result){
                $this->success('添加成功！');
            }else{
                $this->error('添加失败！');
            }
            return $input;
        }
        return $this->fetch('member/member-add');
    }
    //编辑会员
    public function member_edit($uid){
        if(request()->isAjax()){
            $input = input();
            $user = new User();
            $validate = new \app\common\validate\From();
            $result = $validate->scene('member_edit')->check($input);
            if(!$result){
                $this->error($validate->getError());
            }
            $status = isset($input['status'])?1:0;
            $username = trim($input['username']);
            $userRes = $user->where('uid','<>',$uid)->where('username',$username)->find();
            if($userRes){
                $this->error('用户名已存在！');
            }
            $balance = trim($input['balance']);
            $btc_balance = trim($input['btc_balance']);
            $phone = trim($input['phone']);
            $phoneRes = $user->where('uid','<>',$uid)->where('phone',$phone)->find();
            if($phoneRes){
                $this->error('手机号已存在！');
            }
            $data = [
                'username'  => $username,
                'balance'   => $balance,
                'btc_balance' => $btc_balance,
                'phone'     => $phone,
                'status'    => $status
            ];
            $password = trim($input['password']);
            if($password != ''){
                if(strlen($password) < 8){
                    $this->error('密码长度至少8位');
                }else{
                    $sort = Common::getSort();//加密盐
                    $password = md5(md5(trim($password)).$sort);
                    $data['password'] = $password;
                    $data['salt'] = $sort;
                }

            }
            $result = $user->where('uid',$uid)->update($data);
            if($result){
                $this->success('修改成功！');
            }
            return;
        }
        $user = new User();
        $fields = ['uid','username','phone','btc_balance','balance','status'];
        $res = $user->field($fields)->find($uid);
        if($res){
            $data = [
                'member'=>$res
            ];
            $this->assign($data);
            return $this->fetch('member/member-edit');
        }else{

        }

    }
    //删除会员
    public function member_del($uid){
        if(request()->isAjax()){
            $result = User::destroy(['uid'=>$uid]);
            if($result){
                $this->success('删除成功！','','url');
            }else{
                $this->error('删除失败！');
            }
        }
    }
    //批量删除会员
    public function member_dels(){
        if(request()->isAjax()){
            $input = input();
            $uids = json_decode($input['uids']);
            if(!$uids){
                $this->error('请选择要删除的数据！');
            }
            $result = User::destroy($uids);
            if($result){
                $this->success('删除成功！','','');
            }else{
                $this->error('删除失败！');
            }


        }
    }
}