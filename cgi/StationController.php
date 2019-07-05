<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/24
 * Time: 21:45
 */


//require_once('util/mysql.php');
require('util/MongoUtil.php');
header("content-type", "application/json;charet=utf-8");


//$arr=query("SELECT * FROM place");
//echo json_encode($arr);



$data=MongoUtil::query("place");
echo json_encode($data);


?>