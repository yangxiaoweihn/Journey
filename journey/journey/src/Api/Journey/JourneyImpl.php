<?php

namespace Journey\Api\Journey;
use Journey\Api\Db\Db as Db;
use Journey\Api\Journey\Entity\Journey as Journey;
use Journey\Api\Utils\DateUtils;
use Journey\Api\Common\ErrorCode as ErrorCode;

/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/12
 * Time: 14:57
 *
 * 旅途内容日志
 */
class JourneyImpl {

    public function addJourney($userId, $title, $content, $authority, array $data) {
        $journey = new Journey();
        $journey->userId = $userId;
        $journey->title = $title;
        $journey->content = $content;
        $journey->authority = $authority;
        $journey->mdDatas = $data;
        return $this->addJourneyWithEntity($journey);
    }

    /**
     * @param Journey $journey
     * @return bool
     */
    public function addJourneyWithEntity(Journey $journey) {
        if (null == $journey || empty($journey->mdDatas) || 0 == $journey->userId) {
            return array(
                'code'=>1000,
                'msg'=>ErrorCode::$ER_PARAM['1000']);
        }

        $sql = "insert into `journey`
            (`user_id`, `title`, `content`, `create_time`, `authority`, `md_datas`) 
            VALUES (?, ?, ?, ?, ?, ?)";

//        var_dump($journey);

        $mysqliStmt = Db::newInstatnce()->connect()->prepare($sql);
        $mysqliStmt->bind_param(
            'isssis',
            $journey->userId,
            $journey->title,
            $journey->content,
            DateUtils::dateToDbFromPhp(time()),
            $journey->authority,
            json_encode($journey->mdDatas)
        );

        $rel = $mysqliStmt->execute();
        $mysqliStmt->close();

        if (!$rel) {
            return array(
                'code'=>2000,
                'msg'=>ErrorCode::$ER_SERVER['2000']
            );
        }
        return array(
            'code'=>ErrorCode::$ER_SUCCESS);
    }

    /**
     * 获取[指定|非指定]用户下的旅途日志
     * @param $userId
     * @param $start
     * @param $pageSize
     * @return array|null
     */
    public function getJourneyBy($userId, $start, $pageSize) {
        $sql = "select 
                    `id`, 
                    `user_id`, 
                    `title`, 
                    `content`, 
                    `create_time`, 
                    `update_time`, 
                    `authority`, 
                    `count_like`, 
                    `md_datas`, 
                    `md_types`
                from `journey` ";
        $sql .= $userId > 0 ? " WHERE `user_id` = $userId " : "";

//        echo $sql;

        $mysqliStmt = Db::newInstatnce()->connect()->query($sql);

        $datas = array();
        while ($row = $mysqliStmt->fetch_assoc()) {
//            var_dump($row);
            $journey = new Journey();
            $journey->id = $row['id'];
            $journey->userId = $row['user_id'];
            $journey->title = $row['title'];
            $journey->content = $row['content'];
            $journey->createTime = $row['create_time'];
            $journey->updateTime = $row['update_time'];
            $journey->authority = $row['authority'];
            $journey->countLike = $row['count_like'];
            $journey->mdTypes = $row['md_types'];
            array_push($datas, $journey);
        }

        $mysqliStmt->close();
        return $datas;
    }
}