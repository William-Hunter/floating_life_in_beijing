<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/2
 * Time: 17:18
 */


require_once('util/mysql.php');
header("content-type", "application/json");


$arr=query("SELECT * FROM place WHERE id=?");


?>

