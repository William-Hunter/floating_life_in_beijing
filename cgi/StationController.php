<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/24
 * Time: 21:45
 */

require('util/MongoUtil.php');
header("content-type", "application/json;charet=utf-8");

echo json_encode(MongoUtil::query('place'));

?>