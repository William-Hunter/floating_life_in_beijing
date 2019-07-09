<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/9
 * Time: 16:13
 */

require('util/MongoUtil.php');

function mystate(){
    return MongoUtil::query("character",[],['limit' => 1])[0];
}


@$func=$_REQUEST['func'];
$result=null;
switch ($func){
    case 'mystate':
        $result=array("code"=>200,"msg"=>"success","state"=>mystate());
        break;
    default:break;
}

if($result!=null){
    echo json_encode($result);
}else{
    echo "null";
}




?>

