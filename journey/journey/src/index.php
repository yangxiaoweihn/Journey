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


//require __DIR__ . '/Journey/Config.php';

//require 'Journey/api/user/entity/User.php';
//require 'Journey/api/db/Config.php';
//require 'Journey/api/db/Db.php';
//require 'Journey/api/user/UserImpl.php';


//use Api\User\UserImpl;
$app->get('/user/userInfo/{id}', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
    $id = (int) $args['id'];
    echo "</br>$id</br>";

//    new \GGG\AA();

    echo "</br>".__DIR__.__DIR_ROOT__."</br>";

    echo "</br>".__NAMESPACE__."</br>";

    $userImpl = new \Journey\Api\User\UserImpl();
    $userImpl->getUserByNickName('tp');
//    $user = $userImpl->getUserById($id);
//    echo "</br>'.$user->id.'</br>";
});





// Run app
$app->run();
