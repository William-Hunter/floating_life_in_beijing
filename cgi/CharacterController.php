<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/9
 * Time: 16:13
 */

require('util/MongoUtil.php');

function myStockAmount(){
    $command = [
        'aggregate' => 'inventory',
        'pipeline' => [
            [
                '$group' => [
                    "_id" => [],
                    "total" => [
                        '$sum' => '$quantity'
                    ]
                ]
            ]
        ],
        'cursor' => (object)[]
    ];
    return  MongoUtil::command($command)[0]['total'];
}

function mystate(){
    $state=MongoUtil::query("character",[],['limit' => 1])[0];
    $state['stocked']=myStockAmount();
    return $state;
}

@$func=$_REQUEST['func'];
$result=null;
switch ($func){
    case 'mystate':
        $result=array("code"=>200,"msg"=>"success","state"=>mystate());
        break;
    default:break;
}

if($result<>null){
    echo json_encode($result);
}else{
    echo "null";
}

?>

