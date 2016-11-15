<?php
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;
use Journey\Api\Response\ApiResponse as ApiResponse;
use Journey\Api\Utils\RequestParamCheck as RequestParamCheck;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/15
 * Time: 17:15
 */
class BaseApi {
    /**
     * 拦截处理请求头数据
     *
     * @param \Slim\Http\Request $request
     * @return bool
     */
    protected function filterHeader(\Slim\Http\Request $request) {
        $check = RequestParamCheck::checkCommonHeaderParams($request->getHeader('api_header'));
        $validate = $check[0];
        if (!$validate) {
            ApiResponse::ResponseWith(-1, $check[1], null);
        }
        return $validate;
    }

    /**
     * @param $body
     * @return array
     */
    protected function parseRequestBody($body) {
        return RequestParamCheck::json($body);
    }
}