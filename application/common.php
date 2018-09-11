<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// 币种转换
function code_replace($type=''){
    if($type == '我的余额(CNY)'){
        $field = 'balance';
    }elseif($type == '我的比特币(BTC)'){
        $field = 'btc_balance';
    }elseif($type == '我的莱特币(LTC)'){
        $field = 'ltc_balance';
    }else{
        $field = 'bch_balance';
    }
    return $field;
}