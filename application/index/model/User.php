<?php
/**
 * Created by PhpStorm.
 * User: Udea_PHP2
 * Date: 2018/1/2
 * Time: 17:17
 */
namespace app\index\model;
use think\Model;

class User extends Model{
    //指定主键
    protected $pk = 'uid';
    //设置表
    protected $table = 'btc_member';
}