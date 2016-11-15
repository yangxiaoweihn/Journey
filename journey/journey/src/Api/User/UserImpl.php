<?php
//require_once ('./entity/User.php');
//require_once ('../db/Config.php');
//require_once ('../db/Db.php');
//require_once ('../utils/DateUtils.php');

namespace Journey\Api\User;

use Journey\Api\User\Entity\User;
use Journey\Api\Db\Db;
use Journey\Api\Utils\DateUtils;

/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/6
 * Time: 20:38
 *
 * 用户数据操作
 */
class UserImpl/* implements IUser */{

    /**
     * 自平台用户注册，nickName唯一
     *
     * @param $nickName
     * @param $moblie
     * @param $loginPswd
     * @param $gender
     * @param $userSource
     * @param $userType
     * @param $userStatus
     * @return array
     */
    public function registerForSelfPlat($nickName, $moblie, $loginPswd, $gender, $userSource, $userType, $userStatus)/* :array */{
        if (empty($nickName)) {
            return array('code'=>false, 'msg'=>'昵称不能为空');
        }
        if (empty($loginPswd)) {
            return array('code'=>false, 'msg'=>'密码不能为空');
        }
        if ($this->isUserNameExist($nickName)) {
            return array('code'=>false, 'msg'=>'昵称已存在');
        }

        $sql = 'insert into '.'user'.' (
            `nick_name`, 
            `mobile`,
            `login_pswd`,      
            `gender`,
            `register_time`,
            `user_status`,
            `user_type`,
            `user_source`
        ) values (?, ?, ?, ?, ?, ?, ?, ?)';

        $mysqli = Db::newInstatnce() -> connect() -> prepare($sql);
        $mysqli -> bind_param('sssisiii', $nickName, $moblie, $loginPswd, $gender, DateUtils::dateToDbFromPhp(time()), $userStatus, $userType, $userSource);
        $mysqli -> execute();
        $mysqli -> close();
        return array('code'=>true, 'msg'=>'注册成功');
    }

    /**
     * 通过用户名获取用户信息
     *
     * @param $nickName
     * @return null|User
     */
    public function getUserByNickName($nickName) {
        if (!$nickName) {
            return null;
        }

        $sql = 'select * from '.'user'.' where `nick_name` = '."'$nickName'";
        return $this->getUserBySql($sql);
    }

    /**
     * 通过id获取用户信息
     *
     * @param $userId
     * @return null|User
     */
    public function getUserById($userId) /*: User*/{
        if (!$userId) {
            return null;
        }

        $sql = 'select * from '.'user'.' where `id` = '.$userId;
        return $this->getUserBySql($sql);
    }

    /**
     * 通过sql语句获取用户信息
     *
     * @param $sql
     * @return null|User
     */
    private function getUserBySql($sql) {
        if (!$sql) {
            return null;
        }
        echo $sql;
        $result = Db::newInstatnce() -> connect() -> query($sql) -> fetch_array();
        if (!$result) {
            echo ',,,,,,,\n';
            return null;
        }
        $user = new Entity\User();
        $user -> id = $result['id'];
        $user -> mobile = $result['mobile'];
        $user -> nickName = $result['nick_name'];
        $user -> loginPswd = $result['login_pswd'];
        $user -> gender = $result['gender'];
        $user -> avatarUrl = $result['avatar_url'];
        $user -> loginToken = $result['login_token'];
        $user -> pushToken = $result['push_token'];
        $user -> registerTime = DateUtils::dataToPhpFromDb($result['register_time']);
        $user -> loginTimeLatest = DateUtils::dataToPhpFromDb($result['login_time_latest']);
        $user -> countConcern = $result['count_concern'];
        $user -> userStatus = $result['user_status'];
        $user -> userType = $result['user_type'];
        $user -> userSource = $result['user_source'];
        $user -> countVisitorBeen = $result['count_visitor_been'];
        $user -> loginStatus = $result['login_status'];
        return $user;
    }

    /**
     * @param $user
     * @return bool
     */
    public function updateUser(User $user) /* : User*/{
        $sql = 'update `user` set '.
            '`nick_name` = ? ,'.
            '`login_pswd` = ? ,'.
            '`gender` = ? ,'.
            '`avatar_url` = ? ,'.
            '`login_token` = ? ,'.
            '`push_token` = ? ,'.
            '`register_time` = ? ,'.
            '`login_time_latest` = ? ,'.
            '`count_concern` = ? ,'.
            '`user_status` = ? ,'.
            '`user_type` = ? ,'.
            '`user_source` = ? ,'.
            '`count_visitor_been` = ? ,'.
            '`mobile` = ? ,'.
            '`login_status` = ? ';

        $mysqliStmt = Db::newInstatnce()->connect()->prepare($sql);
        $mysqliStmt -> bind_param('ssisssssiiiiisi',
            $user -> nickName,
            $user -> loginPswd,
            $user -> gender,
            $user -> avatarUrl,
            $user -> loginToken,
            $user -> pushToken,
            DateUtils::dateToDbFromPhp($user -> registerTime),
            DateUtils::dateToDbFromPhp($user -> loginTimeLatest),
            $user -> countConcern,
            $user -> userStatus,
            $user -> userType,
            $user -> userSource,
            $user -> countVisitorBeen,
            $user -> mobile,
            $user -> loginStatus);
        $rel = $mysqliStmt -> execute();
        $mysqliStmt -> close();
        return $rel;
    }

    /**
     * @param $nickNameOrMobile
     * @param $pswd
     * @return array
     */
    public function login($nickNameOrMobile, $pswd) {
        if (empty($nickNameOrMobile) || empty($pswd)) {
            return array('code'=>false, 'msg'=>'参数不能为空', 'data'=>null);
        }
        $sql = 'select * from '.'user'.' where `nick_name` = '."'$nickNameOrMobile'".' or `mobile` = '."'$nickNameOrMobile'".' and `login_pswd` = '."'$pswd'";
        $user = $this -> getUserBySql($sql);
        if (!$user) {
            return array('code'=>false, 'msg'=>'用户不存在', 'data'=>null);
        }

        $user -> loginTimeLatest = time();
        $user -> loginStatus = true;

        if (false == $this -> updateUser($user)) {
            return array('code'=>false, 'msg'=>'服务异常', 'data'=>null);
        }
        return array('code'=>true, 'msg'=>'登录成功', 'data'=>$user);
    }

    /**
     * 注销操作
     * @param $userId
     * @return array
     */
    public function logout($userId) {
        if (empty($userId)) {
            return array('code'=>false, 'msg'=>'参数不能为空');
        }

        $null = "''";
        $sql = 'update user set `login_token` = '.$null.', `push_token` = '.$null.', `login_status` = false where id = '.$userId;
        $mysql = Db::newInstatnce()->connect();
        $mysql->query($sql);
        $mysql->close();
        return array('code'=>true, 'msg'=>'注销成功');
    }


    /**
     * 判断昵称是否存在
     *
     * @param $nickName
     *
     * @return bool
     */
    private function isUserNameExist($nickName)/* : bool */{
        $sql = 'select `id` from '.'user'.' where `nick_name` = '."'$nickName'";
        $connect = Db::newInstatnce() -> connect();
        return $connect -> query($sql) -> fetch_row() > 0;
    }
}
