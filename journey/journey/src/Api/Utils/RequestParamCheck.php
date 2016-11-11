<?php
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/10
 * Time: 14:22
 */

namespace Journey\Api\Utils;

/**
 * 请求头数据校验
 *
 * 整体请求数据格式为
 * {
        "api_header":{
        },
        "body":{
        }
    }
 *
 * 其中header节点将会放在http header中
 * POST请求时需要提交body数据
 *
 * Class RequestParamCheck
 * @package Journey\Api\Utils
 */
class RequestParamCheck {

    /**
     * 接口头数据校验
     * @param $headers array
     * @return array
     */
    public static function checkCommonHeaderParams(array $headers) {
        if (!is_array($headers) || count($headers) == 0) {
            return array(false, '缺少api头部');
        }

        $rel = RequestParamCheck::json($headers[0]);
        if (false == $rel[0]) {
            return array(false, 'api头部格式错误');
        }

        $data = $rel[0];
        //TODO 数据读取及校验

        return array(true, '');
    }

    /**
     * 判断是否为json数据
     * @param $string
     * @return bool
     */
    private static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 解析json结果
     * @param $string
     * @return array
     */
    private static function json($string) {
        $json = json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE) ? array(true, $json) : array(false, null);
    }
}

