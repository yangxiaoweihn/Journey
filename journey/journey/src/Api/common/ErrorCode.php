<?php
namespace Journey\Api\Common;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/14
 * Time: 17:09
 */

class ErrorCode{
    //正常
    public static $ER_SUCCESS = 0;
    //参数错误
    public static $ER_PARAM = array(
        '1000'=>'缺少参数',
        '1001'=>'参数类型错误'
    );

    //服务异常
    public static $ER_SERVER = array(
        '2000'=>'操作异常'
    );
}