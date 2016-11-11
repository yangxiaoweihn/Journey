<?php
namespace Journey\Api\Utils;
date_default_timezone_set('PRC');
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/8
 * Time: 15:56
 */
class DateUtils {
    /**
     * 将指定php日期转化为数据库时间戳日期格式
     * @param $time
     * @return false|string
     */
    public static function dateToDbFromPhp($time) {
        return date('Y-m-d H:i:s', $time);
    }

    /**
     * 将指定db日期转化为php日期
     * @param $time
     * @return false|int
     */
    public static function dataToPhpFromDb($time) {
        return strtotime($time);
    }
}