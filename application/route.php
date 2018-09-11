<?php

use think\Route;

//测试方法
Route::get('/test','index/Test/test');


//========前台路由===============
Route::get('/','index/Index/index');
Route::any('/login','index/Login/login',['method'=>'get|post']);
Route::any('/register','index/Login/register',['method'=>'get|post']);
Route::any('/more/:id','index/index/more',['method'=>'get|post']);
// Route::any('/testajax','index/index/testajax',['method'=>'get|post']);
// Route::any('/returnajax','index/index/returnajax',['method'=>'get|post']);
//个人中心
Route::group(['name'=>'member','prefix'=>'index/'],function(){
    //个人中心
    //Route::get('/center','Member/center',['merge_extra_vars'=>true]);
    //账户信息
    Route::get('/member_info','Member/memberInfo',['merge_extra_vars'=>true]);
    // 我的钱包
    Route::get('/my_wallet','Member/mywallet',['merge_extra_vars'=>true]);
    //币种转化
    Route::any('/currency_replace','Member/currency_replace',['merge_extra_vars'=>true]);
    //余额提现
    Route::any('/putforward','Member/putforward',['merge_extra_vars'=>true]);
    //修改账户密码
    Route::post('/changePwd','Member/changePwd',['merge_extra_vars'=>true]);
    //更换手机号
    Route::post('/changePhone','Member/changePhone',['merge_extra_vars'=>true]);
    //我的订单
    Route::get('/my_order','Member/myOrder',['merge_extra_vars'=>true]);
    //订单详细信息
    Route::get('/orderDetailInfo/:orderid','Member/orderDetailInfo',['merge_extra_vars'=>true]);
    //我的合约
    Route::any('/myContract','Member/myContract',['merge_extra_vars'=>true]);
    //合约详情
    Route::any('/Contract_detail','Member/Contract_detail',['merge_extra_vars'=>true]);
    //配送地址
    Route::get('/shipping_address','Address/shippingAddress',['merge_extra_vars'=>true]);
    
    //添加配送地址
    Route::any('/addAddress','ShopCart/addAddress',['method'=>'get|post']);
    //编辑配送地址
    Route::get('/editAddress/:id','Address/editAddress');
    Route::post('/editAddress','Address/editAddress');
    //删除配送地址
    Route::post('/delAddress','Address/delAddress');
    //个人中心支付订单
    Route::get('/pay_order/:oid','Member/payOrder');
    //个人中心删除订单
    Route::get('/del_order/:oid','Member/delOrder');
});

//矿机列表
Route::get('/mill_list','index/Mill/mill_list');
//矿机详情
Route::get('/mill_detail/:id','index/Mill/mill_detail');
//我的购物车
Route::any('/myCarts','index/ShopCart/myCarts',['method'=>'get|post']);
//加入购物车
Route::post('/addCart','index/ShopCart/addCart');
//收费明细
Route::any('/chargeDetail/:mid','index/ShopCart/chargeDetail');
//提交订单
Route::post('/addOrder','index/ShopCart/addOrder');
//删除购物车订单
Route::get('/delOrder/:id','index/ShopCart/delOrder');
//购物车商品数量增减
Route::any('/cartProEdit','index/ShopCart/cartProEdit');
//搜索列表
Route::any('/search','index/Mill/search');


//订单支付页面
Route::get('/orderBuy/:orderid','index/OrderBuy/orderBuy');

//退出登录
Route::any('/logout','index/Login/logout');
//获取验证码接口
Route::post('/getNoteCode','index/Login/getNoteCode');


//==========后台路由========
//首页路由
Route::group(['name'=>'admin','prefix'=>'admin/Index/'],function(){
    Route::get('/','index',['merge_extra_vars'=>true]);
    Route::get('/welcome','welcome',['merge_extra_vars'=>true]);
});
//登录路由
Route::group(['name'=>'admin','prefix'=>'admin/Login/'],function(){
    Route::any('/login','login',['merge_extra_vars'=>true,'method'=>'get|post']);
    Route::any('/logout','logout',['merge_extra_vars'=>true,'method'=>'get|post']);
});

//会员管理路由
Route::group(['name'=>'admin/member','prefix'=>'admin/Member/'],function(){
    Route::get('/member_list','member_list',['merge_extra_vars'=>true]);
    Route::any('/member_edit/:uid','member_edit',['method'=>'get|post']);
    Route::get('/member_del/:uid','member_del');
    Route::any('/member_add','member_add',['method'=>'get|post']);
    Route::post('/member_dels','member_dels');
});

//矿机管理路由
Route::group(['name'=>'admin/mill','prefix'=>'admin/Mill/'],function(){
    Route::get('/mill_list','mill_list',['merge_extra_vars'=>true]);
    Route::any('/mill_edit/:mid','mill_edit',['method'=>'get|post']);
    Route::get('/mill_del/:mid','mill_del');
    Route::any('/mill_add','mill_add',['method'=>'get|post']);
    Route::post('/mill_dels','mill_dels');
    Route::any('/mill_length','mill_length');
    Route::any('/mill_length_del/:uid','mill_length_del');
    Route::any('/mill_length_dels','mill_length_dels');
    Route::any('/mill_length_add','mill_length_add');
    Route::any('/mill_length_edit/:uid','mill_length_edit');
});
//矿机图片文件上传接口
Route::any('/uploadMillImg','admin/Upload/uploadMillImg');
//富文本编辑器文件上传接口
Route::any('/uploadFile','admin/Upload/uploadFile');






//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//
//];
