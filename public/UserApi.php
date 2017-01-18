<?php
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;
use Journey\Api\Response\ApiResponse as ApiResponse;
use Journey\Api\User\UserImpl as UserImpl;
use Journey\Api\Common\ErrorCode as ErrorCode;
use Journey\Api\Common\Common as Common;
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
        $this->concernUser();
        $this->getConcernUserList();
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

    /**
     * GET
     *
     * 获取用户信息
     *
     * id
     */
    private function getUserInfo() {
        $this->app->get('/user/userInfo/{id}', function (SlimRequest $request, SlimResponse $response, $args) {
            $id = (int) $args['id'];

            $userImpl = new UserImpl;
            $user = $userImpl->getUserByNickName('tp');
            var_dump(json_encode($user));
        });
    }

    /**
     * POST
     *
     * 关注、取消关注
     *
     * action: 1 关注 0 取消关注
     * concernUserId: 主动关注方
     * beConcernedUserId: 被关注方
     */
    private function concernUser() {
        $this->app->post('/user/concern', function (SlimRequest $request, SlimResponse $response, $args) {
            $action = $request->getParam('action');
            $concernUserId = $request->getParam('concernUserId');
            $beConcernedUserId = $request->getParam('beConcernedUserId');

            if (!in_array($action, array(0, 1)) || !is_numeric($concernUserId) || !is_numeric($beConcernedUserId)) {
                $code = ErrorCode::ER_PARAM_TYPE_DIS;
                ApiResponse::ResponseWith($code, ErrorCode::$ER_PARAM[$code]);
                return;
            }

            echo $action.' - '.$concernUserId.' - '.$beConcernedUserId;

            $userImpl = new UserImpl;
            if (1 == $action) {
                $rel = $userImpl->concernUser($concernUserId, $beConcernedUserId);
                if ($rel) {
                    ApiResponse::ResponseWith(ErrorCode::ER_SUCCESS, "关注成功");
                }else {
                    ApiResponse::ResponseWith(ErrorCode::ER_SERVER_OPERATE, "关注失败");
                }
            }else {
                $rel = $userImpl->cancleConcernUser($concernUserId, $beConcernedUserId);
                if ($rel) {
                    ApiResponse::ResponseWith(ErrorCode::ER_SUCCESS, "取消成功");
                }else {
                    ApiResponse::ResponseWith(ErrorCode::ER_SERVER_OPERATE, "取消失败[不存在]");
                }
            }
        });
    }

    /**
     * GET
     *
     * 获取关注列表[分页]
     *
     * userId
     * Common::PARAM_PAGEOFFSET
     * Common::PARAM_PAGESIZE
     */
    private function getConcernUserList() {
        $this->app->get('/user/getConcernList', function (SlimRequest $request, SlimResponse $response, $args) {
            $userId = $request->getParam('userId');

            if (!self::checkUserId($userId)) {
                return ;
            }

            $pageOffset = self::checkIntParamAndSetDefault($request->getParam(Common::PARAM_PAGEOFFSET));
            $pageSize = self::checkIntParamAndSetDefault($request->getParam(Common::PARAM_PAGESIZE));

            $user = new UserImpl();
            $list = $user->getConcernUserList($userId, $pageOffset, $pageSize);

            ApiResponse::ResponseWith(ErrorCode::ER_SUCCESS, '获取成功', $list);
        });
    }
}