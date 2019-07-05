<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/2
 * Time: 17:18
 */


require_once('util/mysql.php');
require('util/MongoUtil.php');
header("content-type", "application/json;charset=UTF-8");

function market_products($placeId){
    if ($placeId==null){
        echo "null";
        return;
    }
    $sql="
        SELECT
            pro.id,pro.name,pro.current_price AS price
        FROM
            product pro,
            product_of_place pop
        WHERE
            pop.product_id = pro.id
        AND pop.place_id = ".$placeId."
	";
    return query($sql);
}

function mystock(){
    $sql="
        SELECT
            inve.id,
            pro.`name`,
            inve.quantity,
            inve.buy_price AS price
        FROM
            inventory inve,
            product pro
        WHERE
            inve.product_id = pro.id
	";
    return query($sql);
}

function market_product_mongo($placeId){
    $place= MongoUtil::queryById("place",$placeId);
    $id=$place['_id'];
    MongoUtil::query("product_of_place",);
}

@$func=$_REQUEST['func'];
@$placeId=$_REQUEST['placeId'];
$result=null;
switch ($func){
    case 'marketInit':
        $arr1=market_product_mongo($placeId);
        
//        $arr2=mystock_mongo();
//        $result=array("code"=>200,"msg"=>"success","market_products"=>$arr1,'mystock'=>$arr2);
        break;
    default:break;
}
if($result!=null){
    echo json_encode($result);
}else{
    echo "null";
}

?>

