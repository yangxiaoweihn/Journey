<?php


require_once("./Entity/User.php");
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/6
 * Time: 20:39
 *
 * 用户数据操作
 */
interface IUser {
    public function register($user);

    public function login();

    public function logout();

}