<?php
namespace Journey\Api\Db;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/7
 * Time: 14:13
 */
class Config {
    private static $dbConfigDebug = array(
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'journey_book',
        'port'      => '3306',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => ''
    );

    private static $dbconfigRelease = array(
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'test',
        'port'      => '3306',
        'username'  => 'test',
        'password'  => 'l4m3p455w0rd!',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => ''
    );

    public static function dbConfig() {
        $isDebug = true;
        return $isDebug ? self::$dbConfigDebug : self::$dbconfigRelease;
    }
}
