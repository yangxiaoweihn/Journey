<?
namespace Journey\Api\User\Entity;

/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/6
 * Time: 20:44
 *
 * 用户数据
 */
class User {
    public $id;
    //用户手机号
    public $mobile;
    public $nickName;
    public $loginPswd;
    //性别  0:女 1:男
    public $gender;
    public $avatarUrl;
    public $pushToken;
    public $loginToken;
    public $registerTime;
    //最近登陆时间
    public $loginTimeLatest;
    //被关注数量
    public $countConcern;
    //用户状态
    public $userStatus;
    //用户类型 -1:系统僵尸用户 0: 一般类型 1: 超级用户
    public $userType;
    //用户来源（注册）0:unknow 1:android 2:ios 3:web
    public $userSource;
    //被访问总量
    public $countVisitorBeen;
    //是否登陆状态
    public $loginStatus;

}