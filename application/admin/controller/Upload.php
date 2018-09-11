<?php
namespace app\admin\controller;

class Upload extends Common{

    //上传矿机图片
    public function uploadMillImg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('mill_img');
        $path = ROOT_PATH . 'public' . DS . 'millImgs';
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,gif'])->move($path);
        if($info){
            $msg = [
                'code'=>0,
                'msg'=>'文件上传成功！',
                'data'=>[
                    'src'=> '/millImgs/'.str_replace('\\','/',$info->getSaveName()),
                ],
            ];
            return $msg;
        }else{
            $msg = [
                'code'=>-1,
                'msg'=>$file->getError(),
            ];
            return $msg;
        }
    }
    //富文本编辑器上传文件
    public function uploadFile(){
        $file = request()->file('file');
        $path = ROOT_PATH . 'public' . DS . 'uploads';
        //最大上传1M文件
        $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,gif'])->move($path);
        if($info){
            $msg = [
                'code'=>0,
                'msg'=>'',
                'data'=>[
                    'src'=> '/uploads/'.str_replace('\\','/',$info->getSaveName()),
                    'title'=>''
                ],
            ];
            return $msg;
        }else{
            $msg = [
                'code'=>-1,
                'msg'=>$file->getError(),
                'data'=>[
                    'src'=> '',
                    'title'=>''
                ],
            ];
            return $msg;
        }
    }
}