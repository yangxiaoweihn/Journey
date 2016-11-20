<?php
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;
use Journey\Api\Response\ApiResponse as ApiResponse;
use Journey\Api\User\UserImpl as UserImpl;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/15
 * Time: 17:08
 */
class UserApi extends BaseApi {
    public function __construct(Slim\App $app) {
        parent::__construct($app);
        $this->userLogin();
        $this->userRegister();
        $this->getUserInfo();
        $this->userLogout();
    }

    private function userRegister() {
        $this->app->post('/user/register', function (SlimRequest $request, SlimResponse $response, $args) {
            if (false == self::filterHeader($request)) {
                return;
            }

            $json = self::parseRequestBody($request->getBody()->getContents());
            if ($json[0] == false) {
                return;
            }

            $data = $json[1];

            $userImpl = new UserImpl();
            $rel = $userImpl->registerForSelfPlat($data->{'nickName'}, '', $data->{'password'}, 0, 0, 0, 0);
            if ($rel['code']) {
                //注册成功
                $rel = $userImpl->login($data->{'nickName'}, $data->{'password'});
            }

            ApiResponse::ResponseWith($rel['code'] ? 0 : 1, $rel['msg'], $rel['data']);
        });
    }

    private function userLogin() {
        /**
         *
         */
        $this->app->post('/user/login', function (SlimRequest $request, SlimResponse $response, $args) {

            if (false == self::filterHeader($request)) {
                return;
            }
            $json = self::parseRequestBody($request->getBody()->getContents());
            if ($json[0] == false) {
                return;
            }

            $data = $json[1];

            $userImpl = new UserImpl();
            $rel = $userImpl->login($data->{'nickName'}, $data->{'password'});

            ApiResponse::ResponseWith($rel['code'] ? 0 : 1, $rel['msg'], $rel['data']);
        });
    }

    /**
     * 用户退出
     * PUT
     */
    private function userLogout() {
        $this->app->put('/user/logout', function () {

        });
    }

    private function getUserInfo() {
        $this->app->get('/user/userInfo/{id}', function (SlimRequest $request, SlimResponse $response, $args) {
            $id = (int) $args['id'];

            $userImpl = new UserImpl;
            $user = $userImpl->getUserByNickName('tp');
            var_dump(json_encode($user));
        });
    }
}