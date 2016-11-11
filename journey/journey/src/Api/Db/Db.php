<?php
namespace Journey\Api\Db;
//require_once ('Config.php');
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/7
 * Time: 09:39
 *
 * 数据库连接操作
 */
class Db {
    private static $instance = null;
    private static $connectSource = null;

    private function __construct() {
    }

    public static function newInstatnce()/* : Db */{
        if (null == self::$instance || !(self::$instance instanceof Db)) {
            self::$instance = new Db();
        }
        return self::$instance;
    }

    public function connect()/* : mysqli */{
        if (null != self::$connectSource) {
            return self::$connectSource;
        }

        $dbConfig = Config::dbConfig();
        self::$connectSource = mysqli_connect(
            $dbConfig['host'],
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            $dbConfig['port']);

        if (!self::$connectSource) {
            die('mysql connect error: '.mysqli_error(self::$connectSource));
        }

        mysqli_select_db(self::$connectSource, $dbConfig['database']);
        mysqli_query(self::$connectSource, 'set names UTF8');
        return self::$connectSource;
    }
}

//$connect = Db::newInstatnce() -> connect();
//var_dump($connect);