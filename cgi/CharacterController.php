<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/9
 * Time: 16:13
 */

require('util/MongoUtil.php');
require('util/Calc.php');



@$func=$_REQUEST['func'];
$result=null;
switch ($func){
    case 'mystate':
        $result=array("code"=>200,"msg"=>"success","state"=>Calc::mystate());
        break;
    default:break;
}

if($result<>null){
    echo json_encode($result);
}else{
    echo "null";
}

?>

