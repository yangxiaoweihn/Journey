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
        $this->userLogin($app);
        $this->userRegister($app);
        $this->getUserInfo($app);
    }

    private function userRegister(Slim\App $app) {
        $app->post('/user/register', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
            if (false == $this->filterHeader($request)) {
                return;
            }

            $json = $this->parseRequestBody($request->getBody()->getContents());
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

    private function userLogin(Slim\App $app) {
        /**
         *
         */
        $app->post('/user/login', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {

            if (false == $this->filterHeader($request)) {
                return;
            }
            $json = $this->parseRequestBody($request->getBody()->getContents());
            if ($json[0] == false) {
                return;
            }

            $data = $json[1];

            $userImpl = new UserImpl();
            $rel = $userImpl->login($data->{'nickName'}, $data->{'password'});

            ApiResponse::ResponseWith($rel['code'] ? 0 : 1, $rel['msg'], $rel['data']);
        });
    }

    private function getUserInfo(Slim\App $app) {
        $app->get('/user/userInfo/{id}', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
            $id = (int) $args['id'];

            $userImpl = new UserImpl;
            $user = $userImpl->getUserByNickName('tp');
            var_dump(json_encode($user));
        });
    }
}