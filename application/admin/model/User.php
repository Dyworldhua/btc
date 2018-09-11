<?php
namespace app\admin\model;
use think\Model;

class User extends Model{
    //指定主键
    protected $pk = 'id';
    //设置表
    protected $table = 'btc_adminuser';
}