<?php
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;
use Journey\Api\Response\ApiResponse as ApiResponse;
use Journey\Api\Utils\RequestParamCheck as RequestParamCheck;
use Slim\App as App;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/15
 * Time: 17:15
 */
class BaseApi {

    protected $app;

    public function __construct(App $app){
        $this->app = $app;
    }


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

    /**
     * userId校验
     * @param $userId
     * @return bool
     */
    protected function checkUserId($userId) {
        if (!is_numeric($userId)) {
            $code = ErrorCode::ER_PARAM_TYPE_DIS;
            ApiResponse::ResponseWith($code, ErrorCode::$ER_PARAM[$code]);
            return false;
        }
        return true;
    }

    /**
     * 参数$param没有设置时设置为默认值，否则原值返回
     * @param $param
     * @param int $default
     * @return int|string
     */
    protected function checkIntParamAndSetDefault($param, $default = 0) {
        return is_numeric($param) ? $param : $default;
    }
}