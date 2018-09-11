<?php
namespace app\admin\controller;
use think\Controller;

class Common extends Controller{
    public function __construct() {
        parent::__construct();
        if(!session('?adminUser')){
            $this->redirect('/admin/login');
        }
    }
}
