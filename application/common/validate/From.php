<?php
namespace app\common\validate;
use think\Validate;
class From extends Validate {
    //自定义验证规则
    protected $regex = [
        'phone' => '1[3|6|5|7|8]\d{9}'
    ];
    protected $rule = [
        'username'=>'require|max:10',
        'password'=>'require|min:8',
        'pwd'=>'require|confirm:password',
        'phone'=>'require|regex:phone',
        'captcha'=>'require|captcha',
        'notecaptcha'=>'require',

        'balance'=>'number',
        'btc_balance'=>'number',

        //添加配送地址
        'name'=>'require',
        'postcode'=>'require',
        'detail_info'=>'require',

        'mill_name'=>'require',//矿机名称
        'mill_force'=>'require|number',//矿机算力
        'mill_price'=>'require|number',//矿机价格
        'mill_number'=>'require|number',//矿机库存
        'mill_buyVal'=>'require|number',//矿机最大购买数量
        'mill_weight'=>'require|number',//矿机重量
        'mill_intro'=>'require',//矿机简介
        'mill_img'=>'require',//矿机图片
        'mill_describe'=>'require',//矿机描述
        'mill_arguments'=>'require',//矿机参数描述
        'mill_saleAfter'=>'require',//矿机售后

    ];


    protected $message = [
        //公共验证参数
        'username.require' => '请填写用户名！',
        'username.max' => '用户名最多10个字符！',
        'password.require' => '请填写密码！',
        'password.min' => '密码至少8位！',
        'pwd.confirm' => '确认密码不一致！',
        'username.email'=>"邮箱格式不正确！",
        'phone.require'=>'请填写手机号！',
        'phone.regex'=>'手机号格式不正确！',
        'captcha.require'=>'请填写验证码！',
        'captcha.captcha'=>'验证码错误！',
        'notecaptcha.require'=>'请填写短信验证码！',

        'balance.number'=>'钱包金额必须是数字类型',
        'btc_balance.number'=>'比特币余额必须是数字类型',

        //添加配送地址
        'name.require'=>'请填写姓名！',
        'postcode.require'=>'请填写邮政编码！',
        'detail_info.require'=>'请填写详细地址信息！',

        //添加矿机
        'mill_name.require'=>'矿机名称必须',
        'mill_force.require'=>'矿机算力必须',
        'mill_price.require'=>'矿机价格必须',
        'mill_number.require'=>'矿机库存必须',
        'mill_buyVal.require'=>'矿机最大购买数量必须',
        'mill_weight.require'=>'矿机重量必须',
        'mill_intro.require'=>'矿机简介必须',
        'mill_img.require'=>'矿机图片必须',
        'mill_describe.require'=>'矿机描述必须',
        'mill_arguments.require'=>'矿机参数描述必须',
        'mill_saleAfter.require'=>'矿机售后服务必须',
        'mill_force.number'=>'矿机算力必须为数字类型',
        'mill_price.number'=>'矿机价格必须为数字类型',
        'mill_number.number'=>'矿机库存必须为数字类型',
        'mill_buyVal.number'=>'矿机最大购买数量必须为数字类型',
        'mill_weight.number'=>'矿机重量必须为数字类型',

    ];
    protected $scene = [
        //前台登录
        'index_login'=>['username','password'],
        //前台注册
        'register'=>['username','password','pwd','phone','captcha','notecaptcha'],
        //获取短信验证码，验证手机号
        'getNoteCode'=>['phone'],
        //修改密码
        'changePwd'=>['password','pwd'],
        //修改手机号
        'changePhone'=>['phone','notecaptcha'],
//        ----前台---------------------
//        ----后台---------------------
        //后台登录
        'admin_login'=>['username','password','captcha'],
        //会员编辑
        'member_edit'=>['username','phone','balance','btc_balance'],
        //添加会员
        'member_add'=>['username','password','phone','balance','btc_balance'],
        //添加配送地址
        'addAddress'=>['name','phone','postcode','detail_info'],
        //添加矿机
        'mill_add'=>['mill_name','mill_force','mill_price','mill_number','mill_buyVal','mill_weight','mill_intro','mill_img','mill_describe','mill_arguments','mill_saleAfter'],
        //编辑矿机提交
        'mill_edit'=>['mill_name','mill_force','mill_price','mill_number','mill_buyVal','mill_weight','mill_intro','mill_describe','mill_arguments','mill_saleAfter'],
    ];
}