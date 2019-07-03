<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/2
 * Time: 17:18
 */


require_once('util/mysql.php');

function market_products(){
    header("content-type", "application/json;charset=UTF-8");
    
    @$placeId=$_REQUEST['placeId'];
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
    header("content-type", "application/json");
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


@$func=$_REQUEST['func'];
$result=null;
switch ($func){
    case 'marketInit':
        $arr1=market_products();
        $arr2=mystock();
        $result=array("code"=>200,"msg"=>"success","market_products"=>$arr1,'mystock'=>$arr2);
        break;
    default:break;
}
if($result!=null){
    echo json_encode($result);
}else{
    echo "null";
}

?>

