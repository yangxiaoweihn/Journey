<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

use Journey\Api\User\UserImpl as UserImpl;
use Journey\Api\Utils\RequestParamCheck as RequestParamCheck;
use Journey\Api\Response\ApiResponse as ApiResponse;

$filterHeader = function() {
    return function (){
        echo "</br>filterHeader</br>";
    };
};


/**
 * 拦截处理请求头数据
 *
 * @param \Slim\Http\Request $request
 * @return bool
 */
function filterHeader(\Slim\Http\Request $request) {
    return RequestParamCheck::checkCommonHeaderParams($request->getHeader('api_header'))[0];
}

/**
 * 注册
 */
$app->post('/user/register', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
    $json = $request->getBody()->getContents();

    if (false == filterHeader($request)) {
        echo '缺少头数据';

        exit;
    }

    $userImpl = new UserImpl();
    $rel = $userImpl->login('tp', '123456');


    ApiResponse::ResponseWith($rel['code'] ? 0 : 1, $rel['msg'], $rel['data']);
});

$app->get('/user/userInfo/{id}', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
    $id = (int) $args['id'];
    echo "</br>$id</br>";

    echo "</br>".__DIR__.__DIR_ROOT__."</br>";

    echo "</br>".__NAMESPACE__."</br>";


    echo (new \Test0\Ggg\AA())->shit();

    $userImpl = new UserImpl;
    $user = $userImpl->getUserByNickName('tp');
    var_dump(json_encode($user));
//    $user = $userImpl->getUserById($id);
//    echo "</br>'.$user->id.'</br>";

    echo "</br>----------</br>";

});





// Run app
$app->run();
