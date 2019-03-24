<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/24
 * Time: 21:45
 */

require_once('entity/Station.php');
header("content-type","application/json");


$station1=new Station();
$station1->id='1';
$station1->title="天安门";

$station2=new Station();
$station2->id='2';
$station2->title="中关村";

$arr = array($station1,$station2);

echo json_encode($arr);


?>