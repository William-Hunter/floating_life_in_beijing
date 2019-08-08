<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/9
 * Time: 16:13
 */

require('util/MongoUtil.php');
require('util/Calc.php');

function recover(){
    $state=MongoUtil::query("character",[],['limit' => 1])[0];
    $state['health']=100;
    $state['money']=2200;
    $state['stock']=100;
    $state['debt']=2200;
    $state['interest']=0;
    $state['crime']=0;
    $state['date']=0;
    $state['stocked']=0;
    MongoUtil::insertOrUpdateById('character',$state);
    return array("code"=>200,"msg"=>"success");
}

@$func=$_REQUEST['func'];
$result=null;
switch ($func){
    case 'reset':
        $result=recover();
        break;
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

