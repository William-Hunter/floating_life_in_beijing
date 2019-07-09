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



function market_product_mongo($placeId){
    $command = [
        'aggregate' => 'place',
        'pipeline' => [
            ['$lookup' => [
                "from" => "product_of_place",
                "localField" => "_id",
                "foreignField" => "place_id",
                "as" => "products"
            ]],
            ['$match' => ['_id' => $placeId]],
            ['$project' => ['products' => 1, '_id' => 0]]
        ],
        'cursor' => (object)[]
    ];
    $row=MongoUtil::command($command);
    $products=$row[0]['products'];
    $market_products=null;
    foreach($products as $product){
        $market_products[]=MongoUtil::queryById('product',$product->product_id);
    }
    return $market_products;
}

function mystock_mongo(){
    $command = [
        'aggregate' => 'inventory',
        'pipeline' => [
            ['$lookup' => [
                "from" => "product",
                "localField" => "product_id",
                "foreignField" => "_id",
                "as" => "stock"
            ]]
        ],
        'cursor' => (object)[]
    ];
    $stocks=MongoUtil::command($command);
    $results=null;
    foreach($stocks as $stock){
        $stock_name=$stock['stock'][0]->name;
        $stock['name']=$stock_name;
        unset($stock['stock']);
        $results[]=$stock;
//        echo json_encode($stock);
    }
    return $results;
}

@$func=$_REQUEST['func'];
@$placeId=$_REQUEST['placeId'];
$result=null;
switch ($func){
    case 'marketInit':
        $arr1=market_product_mongo($placeId);
        $arr2=mystock_mongo();
        $result=array("code"=>200,"msg"=>"success","market_products"=>$arr1,'mystock'=>$arr2);
        break;
    default:break;
}

if($result!=null){
    echo json_encode($result);
}else{
    echo "null";
}



/*
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
 */


?>



