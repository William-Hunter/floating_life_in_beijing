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
    }
    return $results;
}

function buy($productId,$amount){
    
    return array("code"=>200,"msg"=>"success","data"=>null);
}

function sell($productId,$amount){
    
    return array("code"=>200,"msg"=>"success","data"=>null);
}

@$func=$_REQUEST['func'];
//@$placeId=$_REQUEST['placeId'];
$result=null;
switch ($func){
    case 'marketInit':
        $arr1=market_product_mongo($_REQUEST['placeId']);
        $arr2=mystock_mongo();
        $result=array("code"=>200,"msg"=>"success","market_products"=>$arr1,'mystock'=>$arr2);
        break;
    case 'buy':
        buy($_REQUEST['productId'],$_REQUEST['amount']);
        break;
    case 'sell':
        sell($_REQUEST['productId'],$_REQUEST['amount']);
        break;
    default:break;
}

if($result!=null){
    echo json_encode($result);
}else{
    echo "null";
}

?>



