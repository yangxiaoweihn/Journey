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
    const ER_SUCCESS        = 0;

    //参数错误
    const ER_PARAM_DISMISS      = 1000;
    const ER_PARAM_TYPE         = 1001;
    const ER_PARAM_TYPE_DIS     = 1002;

    //参数错误
    public static $ER_PARAM = array(
        self::ER_PARAM_DISMISS=>'缺少参数',
        self::ER_PARAM_TYPE=>'参数类型错误',
        self::ER_PARAM_TYPE_DIS=>'缺少参数或参数类型错误'

    );

    //服务异常
    const ER_SERVER_OPERATE        = 2000;
    const ER_SERVER_NOT_EXIST      = 2001;

    //服务异常
    public static $ER_SERVER = array(
        self::ER_SERVER_OPERATE=>'操作异常',
        self::ER_SERVER_NOT_EXIST=>'记录不存在'
    );

    public static $ER_ALL = array(
        self::ER_PARAM_DISMISS=>'缺少参数',
        self::ER_PARAM_TYPE=>'参数类型错误',
        self::ER_PARAM_TYPE_DIS=>'缺少参数或参数类型错误',
        self::ER_SERVER_OPERATE=>'操作异常',
        self::ER_SERVER_NOT_EXIST=>'记录不存在'
    );
}