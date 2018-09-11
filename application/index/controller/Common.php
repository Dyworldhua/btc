<?php
namespace app\index\controller;
use think\Controller;

class Common extends Controller{
    public function __construct() {
        parent::__construct();
        if(!session('?indexUser') || !session('?indexUserId')){
            return $this->redirect('/login');
        }
    }
}