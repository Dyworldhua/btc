<?php
namespace app\admin\controller;

class Index extends Common{
    public function index(){
        return $this->fetch('index/index');
    }
    public function welcome(){
        return $this->fetch('index/welcome');
    }
}