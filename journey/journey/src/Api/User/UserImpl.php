<?php
//require_once ('./entity/User.php');
//require_once ('../db/Config.php');
//require_once ('../db/Db.php');
//require_once ('../utils/DateUtils.php');

namespace Journey\Api\User;

use Journey\Api\User\Entity\User;
use Journey\Api\Db\Db;
use Journey\Api\Utils\DateUtils;
use phpDocumentor\Reflection\Types\Boolean;

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
        $user -> registerTime = DateUtils::dateToPhpFromDb($result['register_time']);
        $user -> loginTimeLatest = DateUtils::dateToPhpFromDb($result['login_time_latest']);
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

    /**
     * 关注
     *
     * * md5(concat(concern_user_id, concerned_user_id))作为唯一冲突键
     * 记录不存在时插入，存在时修改status字段的值
     *
     * @param $concernUserId
     * @param $beConcernedUserId
     * @return bool
     */
    public function concernUser($concernUserId, $beConcernedUserId) {
        $status = 1;
        $sql = 'INSERT into `user_concern` '.
            '(`concern_user_id`, `concerned_user_id`, `status`, `_unique`, `concern_time`)'.
            'VALUES '.
            '(?, ?, ?, md5(concat(`concern_user_id`, `concerned_user_id`)), ?)'.
            'ON DUPLICATE KEY UPDATE `status` = values(`status`), `concern_time` = ?';

        $stmt = Db::newInstatnce()->connect()->prepare($sql);
        $time = DateUtils::dateToDbFromPhp(time());
        $stmt->bind_param('iiiss', $concernUserId, $beConcernedUserId, $status, $time, $time);
        $stmt->execute();
        $row = $stmt->affected_rows;
        $stmt->close();
        return $row != 0;
    }

    /**
     * 取消关注
     * @param $concernUserId
     * @param $beConcernedUserId
     * @return bool
     */
    public function cancleConcernUser($concernUserId, $beConcernedUserId) {
        $status = 0;
        $sql = 'update `user_concern` set `status` = ?, `concern_canceled_time` = ? where `concern_user_id` = ? && `concerned_user_id` = ? ';
        $stmt = Db::newInstatnce()->connect()->prepare($sql);
        $stmt->bind_param('isii', $status, DateUtils::dateToDbFromPhp(time()), $concernUserId, $beConcernedUserId);
        $stmt->execute();
        $row = $stmt->affected_rows;
        $stmt->close();
        return $row != 0;
    }


    /**
     * 获取关注列表
     *
     * @param $concernUserId
     * @param $pageOffset
     * @param $pageSize
     * @return array
     */
    public function getConcernUserList($concernUserId, $pageOffset = 0, $pageSize = 0) {
        echo $concernUserId.'-'.$pageOffset.'-'.$pageSize."\n\n";

        $isPageStartWhere = $pageOffset > 0;
        //分页起始位置
        $pageStartWhere = $isPageStartWhere ? ' and user_concern.id < ? ' : '';

        $sql = 'select
                  user.`user_name`, user.`nick_name`, user.`gender`, user.`avatar_url`,
                  user.`register_time`, user.`count_concern`,
                  user_concern.`concern_time`, user_concern.`concerned_user_id`,
                  user_concern.`id` as page_index
                from
                  user, user_concern
                where
                  user_concern.concern_user_id = ?
                and
                  user.id = user_concern.concern_user_id
                and
                  user_concern.status = 1'.
                $pageStartWhere.'
                order by 
                  user_concern.`id` desc 
                limit ?';

        $stmt = Db::newInstatnce()->connect()->prepare($sql);
        if($isPageStartWhere == true) {
            echo '>>>> '.$pageStartWhere;
            $stmt->bind_param('iii', $concernUserId, $pageOffset, $pageSize);
        }else {
            $stmt->bind_param('ii', $concernUserId, $pageSize);
        }
        $stmt->execute();
        $stmt->bind_result(
            $userName, $nickName, $gender, $avatarUrl, $registerTime,
            $countConcern, $concernTime, $beConcernedUserId, $pageIndex);

        $result = array();
        while ($stmt->fetch()) {
            $record = array(
                'userName'=>$userName,
                'nickName'=>$nickName,
                'gender'=>$gender,
                'avatarUrl'=>$avatarUrl,
                'registerTime'=>DateUtils::dateToPhpFromDb($registerTime),
                'countConcern'=>$countConcern,
                'concernTime'=>DateUtils::dateToPhpFromDb($concernTime),
                'beConcernedUserId'=>$beConcernedUserId,
                'pageIndex'=>$pageIndex
            );
            array_push($result, $record);
        }

        $stmt->close();

        return $result;
    }

}
