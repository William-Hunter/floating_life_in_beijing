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
    
    @$placeId=$_POST['placeId'];
    if ($placeId==null)return ;
    $sql="
        SELECT
            pro.name,pro.current_price AS price
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
            inve.buy_price
        FROM
            inventory inve,
            product pro
        WHERE
            inve.product_id = pro.id
	";
    return query($sql);
}


@$func=$_REQUEST['func'];
$arr;
switch ($func){
    case 'market_products':
        $arr=market_products();
        break;
    case 'mystock':
        $arr=mystock();
        break;
    default:break;
}
if($arr!=null){
    echo json_encode($arr);
}else{
    echo "";
}

?>

