<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/24
 * Time: 21:45
 */


require_once('util/mysql.php');
header("content-type", "application/json");


$arr=query("SELECT * FROM place");

echo json_encode($arr);


?>