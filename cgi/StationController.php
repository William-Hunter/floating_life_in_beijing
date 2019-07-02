<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/24
 * Time: 21:45
 */

require_once('entity/Station.php');
require_once('util/mysql.php');
header("content-type", "application/json");


$arr=query("SELECT * FROM place");

//$arr = array();
//$station = new Station();
//$station->id = '1';
//$station->title = "昆明老街";
//array_push($arr, $station);
//
//$station = new Station();
//$station->id = '2';
//$station->title = "北京王府井";
//array_push($arr, $station);
//
//
//$station = new Station();
//$station->id = '2';
//$station->title = "新疆喀什";
//array_push($arr, $station);
//
//
//$station = new Station();
//$station->id = '2';
//$station->title = "香港九龙";
//array_push($arr, $station);


echo json_encode($arr);

?>