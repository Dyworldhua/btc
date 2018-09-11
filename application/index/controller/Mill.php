<?php
namespace app\index\controller;
use app\index\model\Mills;
use app\index\model\MillsTime;
use think\Db;
class Mill extends Common{
    //矿机列表
    public function mill_list(){
        if(request()->isAjax()){
            $mill = new Mills();
            $fields = [
                'mid','mill_name','mill_price',
                'mill_intro','mill_img'
            ];
            $res = $mill->field($fields)->paginate(15);
            return $res;
        }
        $data = [
            'title'=>'矿机列表'
        ];
        $this->assign($data);
        return $this->fetch('index/mill-list');
    }
    //矿机详情
    public function mill_detail($id){
        $mill = new Mills();
        if(request()->isAjax()){

        }
        $fields = [
            'm.mid','m.mill_name',
            'm.mill_price','m.mill_intro',
            'm.mill_img','m.mill_weight',
            'm.mill_power','m.mill_buyVal',
            'md.mill_describe','md.mill_saleAfter',
            'md.mill_arguments'
        ];
        $res = $mill->alias('m')
            ->field($fields)
            ->where('m.mid',$id)
            ->join('mill_detail md','m.mid = md.mid')
            ->find();
        $data = [
            'title'=>'矿机详情',
        ];
        $milltime = new MillsTime();
        $field = [
            'id','mouth'
        ];
        $result = $milltime->field($field)->select();
        if($result){
            $data['milltime'] = $result;
        }else{
            $this->error('数据错误！');
        }
        if($res){
            $data['mill'] = $res;
        }else{
            $this->error('数据错误！');
        }
        $this->assign($data);
        return $this->fetch('index/mill-detail');
    }
}