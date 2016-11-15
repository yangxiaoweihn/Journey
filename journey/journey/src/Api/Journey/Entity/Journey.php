<?php

namespace Journey\Api\Journey\Entity;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/12
 * Time: 14:58
 */
class Journey {
    public $id = 0;
    public $userId = 0;
    public $title = '';
    public $content = '';
    public $createTime = '';
    public $updateTime = '';
    public $authority;
    public $countLike = 0;
    public $mdDatas = array();
    public $mdTypes;
}