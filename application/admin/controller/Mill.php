<?php
namespace app\admin\controller;
use app\admin\model\Mills;
use app\admin\model\MillDetail;
use app\admin\model\MillsLength;
class Mill extends Common{
    //矿机列表
    public function mill_list(){
        if(request()->isAjax()){
            $mill = new Mills();
            $fields = [
                'm.mid','mill_name',
                'mill_price','mill_force',
                'addTime','mill_number',
                'mill_intro','mill_img',
                'mill_weight','mill_buyVal',
                'mill_describe','mill_arguments',
                'mill_saleAfter'
            ];
            $limit = (null !== input('limit'))?input('limit'):20;
            $res = $mill->alias('m')->field($fields)->join('btc_mill_detail md','m.mid = md.mid')->paginate($limit);
            if($res){
                $result = json_decode(json_encode($res));
                $data = [
                    'code'=>1,
                    'count'=>$result->total,
                    'msg'=>'',
                    'data'=>$result->data
                ];
                return $data;
            }
        }

        return $this->fetch('mill/mill-list');
    }
    //添加矿机
    public function mill_add(){
        if(request()->isAjax()){
            $input = input();

            $data = $input['data'];
            $data['mill_describe'] = $input['mill_describe'];
            $data['mill_arguments'] = $input['mill_arguments'];
            $data['mill_saleAfter'] = $input['mill_saleAfter'];
            unset($data['file']);
            $validate = new \app\common\validate\From();
            $result = $validate->scene('mill_add')->check($data);
            if(!$result){
                $this->error($validate->getError());
            }
            //添加矿机
            $mid = self::getMid();
            $millData = [
                'mid'=>$mid,
                'mill_name'=>trim($data['mill_name']),
                'mill_force'=>trim($data['mill_force']),
                'mill_price'=>trim($data['mill_price']),
                'mill_number'=>trim($data['mill_number']),
                'mill_buyVal'=>trim($data['mill_buyVal']),
                'mill_power'=>trim($data['mill_power']),
                'mill_weight'=>trim($data['mill_weight']),
                'mill_intro'=>trim($data['mill_intro']),
                'mill_img'=>trim($data['mill_img']),
                'addTime'=>time(),
            ];
            $mill = new Mills();
            $mill->data($millData);
            $res = $mill->save();
            if($res){
                $desData = [
                    'mid'=>$mid,
                    'mill_describe'=>trim($data['mill_describe']),
                    'mill_arguments'=>trim($data['mill_arguments']),
                    'mill_saleAfter'=>trim($data['mill_saleAfter']),
                ];
                $millDes = new MillDetail();
                $millDes->data($desData);
                $res = $millDes->save();
                if($res){
                    $this->success('添加成功！','','');
                }
            }else{
                $this->error('添加失败！');
            }

        }
        return $this->fetch('mill/mill-add');
    }
    //编辑矿机
    public function mill_edit($mid){
        //编辑提交
        if(request()->isAjax()){
            $input = input();
            $data = $input['data'];
            $data['mill_describe'] = $input['mill_describe'];
            $data['mill_arguments'] = $input['mill_arguments'];
            $data['mill_saleAfter'] = $input['mill_saleAfter'];
            unset($data['file']);
            $validate = new \app\common\validate\From();
            $result = $validate->scene('mill_edit')->check($data);
            if(!$result){
                $this->error($validate->getError());
            }
            $mid = $input['mid'];
            $millData = [
                'mill_name'=>trim($data['mill_name']),
                'mill_force'=>trim($data['mill_force']),
                'mill_price'=>trim($data['mill_price']),
                'mill_number'=>trim($data['mill_number']),
                'mill_buyVal'=>trim($data['mill_buyVal']),
                'mill_power' =>trim($data['mill_power']),
                'mill_weight'=>trim($data['mill_weight']),
                'mill_intro'=>trim($data['mill_intro']),
            ];
            if(!empty($input['data']['mill_img'])){
                $millData['mill_img'] = trim($input['data']['mill_img']);
            }
            $mill = new Mills();
            $res = $mill->save($millData,['mid'=>$mid]);
            if($res){
                $desData = [
                    'mill_describe'=>trim($data['mill_describe']),
                    'mill_arguments'=>trim($data['mill_arguments']),
                    'mill_saleAfter'=>trim($data['mill_saleAfter']),
                ];
                $millDes = new MillDetail();
                $millDes->save($desData,['mid'=>$mid]);
                $this->success('修改成功！','','');

            }else{
                $this->error('修改失败！');
            }
        }
        $mill = new Mills();
        $fields = [
            'm.mid',
            'mill_name',
            'mill_force',
            'mill_price',
            'mill_number',
            'mill_buyVal',
            'mill_power',
            'mill_weight',
            'mill_intro',
            'mill_img',
            'mill_describe',
            'mill_arguments',
            'mill_saleAfter',
        ];
        $res = $mill->alias('m')
            ->field($fields)
            ->where('m.mid',$mid)
            ->join('btc_mill_detail bmd','m.mid = bmd.mid')
            ->find();
        $data = [
            'mill'=>$res
        ];
        $this->assign($data);
        return $this->fetch('mill/mill-edit');
    }
    //删除矿机
    public function mill_del($mid){
        if(request()->isAjax()){
            $res = Mills::destroy(['mid'=>$mid]);
            if($res){
                $res = MillDetail::destroy(['mid'=>$mid]);
                if($res){
                    $this->success('删除成功！','','');
                }
            }else{
                $this->error('删除失败！');
            }
        }
    }
    //批量删除矿机
    public function mill_dels(){
        if(request()->isAjax()){
            $mids = json_decode(input('post.mids'));
            if(!$mids){
                $this->error('请选择要删除的数据！');
            }
           $res = Mills::destroy($mids);
            if($res){
               $res = MillDetail::where('mid','IN',$mids)->delete();
                if($res){
                    $this->success('删除成功！','','');
                }
            }else{
                $this->error('删除失败！');
            }
        }
    }
    static public function getMid(){
        $mid = rand(1000,99999999);
        $mill = new Mills();
        $res = $mill->where('mid',$mid)->find();
        if($res){
            self::getMid();
        }
        return $mid;
    }
    // 购买时长列表
    public function mill_length(){
            if(request()->isAjax()){
            $length = new MillsLength();
            $field = [
                'id','mouth'
            ];
            $result = $length->field($field)->paginate(90); // 取出购买时长

            if($result){
                $result = json_decode(json_encode($result));
                // var_dump($result);exit;
                $data = [
                    'code'=>1,
                    'count'=>$result->total,
                    'msg'=>'',
                    'data'=>$result->data
                ];
                return $data;
            }
        }
        return $this->fetch('mill/mill_length');
    }
    // 删除购买时长
    public function mill_length_del($uid){
        if(request()->isAjax()){
            $result = MillsLength::destroy(['id'=>$uid]);
            if($result){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }
    // 批量删除购买时长
    public function mill_length_dels(){
        if(request()->isAjax()){
            $input = input();
            $uids = json_decode($input['uids']);
            if(!$uids){
                $this->error('请选择要删除的数据！');
            }
            $result = MillsLength::destroy($uids);
            if($result){
                $this->success('删除成功！','','');
            }else{
                $this->error('删除失败！');
            }
        }
    }
    // 添加购买时长
    public function mill_length_add(){
        if(request()->isAjax()){
            $input = input();
            $length = $input['length']; // 获取购买月份
            $link = new MillsLength();
            $result = $link->insert(['mouth'=>$length]);
            if($result){
                $data = [
                    'code' => 1,
                    'msg' => '添加成功'
                ];
                return $data;
            }
        }
        return $this->fetch('mill/mill_length_add');
    }
    public function mill_length_edit($uid){
        if(request()->isAjax()){
            $input = input();
            $mouth = $input['length']; // 获取购买时长
            $length = new MillsLength();
            $update = $length->where('id',$uid)->update(['mouth'=>$mouth]);
            if($update){
                $data = [
                    'code' => 1,
                    'msg' => '修改成功！' 
                ];
                return $data;
            }
        }
        $length = new MillsLength();
        $field = [
            'id','mouth'
        ];
        $result = $length->field($field)->where('id',$uid)->find();
        $this->assign('length',$result);
        return $this->fetch('mill/mill_length_edit');
    }
}