<?php
namespace app\index\model;
use think\Model;

class Order extends Model{
    protected $pk = 'id';
    protected $table = 'btc_order';
}