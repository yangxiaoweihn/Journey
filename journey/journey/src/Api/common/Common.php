<?php
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/15
 * Time: 17:45
 */

namespace Journey\Api\Common;

//请求头参数
define('Param_ApiHeader', 'api_header', true);

//分页
//define('Param_PageSize', 'pageSize', true);
//define('Param_PageOffset', 'pageOffset', true);
//define('pageSize', 20, true);

class Common{
    //分页
    const PARAM_PAGESIZE         = 'pageSize';
    const PARAM_PAGEOFFSET       = 'pageOffset';
    const PAGE_SIZE              = 20;
}