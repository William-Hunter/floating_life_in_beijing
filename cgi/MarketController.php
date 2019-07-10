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


function market_product_mongo ($placeId) {
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
    $row = MongoUtil::command($command);
    $products = $row[0]['products'];
    $market_products = null;
    foreach ($products as $product) {
        $market_products[] = MongoUtil::queryById('product', $product->product_id);
    }
    return $market_products;
}

function mystock_mongo () {
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
    $stocks = MongoUtil::command($command);
    $results = null;
    foreach ($stocks as $stock) {
        $stock_name = $stock['stock'][0]->name;
        $stock['name'] = $stock_name;
        unset($stock['stock']);
        $results[] = $stock;
    }
    return $results;
}

/**
 * @param $productId
 * @param $amount
 * @return array
 * @throws \MongoDB\Driver\Exception\Exception
 */
function buy ($productId, $amount) {
    try{
        $product = MongoUtil::queryById('product', $productId);
        $final_per_price = null;
        $final_aomount = null;
        $final_spend=null;
        $filter = ['product_id' => $product['_id']];
        $options = ['limit' => 1];
        $rows = MongoUtil::query('inventory', $filter, $options);
        $inventory=null;
        if ($rows <> null) {
            $inventory = $rows[0];
            $final_aomount = $amount + $inventory['quantity'];
            $final_spend=($inventory['buy_price']* $inventory['quantity']) + ($product['current_price'] * $amount);
            $final_per_price = $final_spend/ $final_aomount;
            $inventory = [
                '_id' => $inventory['_id'],
                'product_id' => $inventory['product_id'],
                'buy_price' => round($final_per_price,2),
                'quantity' => $final_aomount
            ];
        } else {
            $final_per_price = $product['current_price'];
            $final_aomount = $amount;
            $final_spend=$product['current_price']*$amount;
            $inventory = [
                '_id' => '2222',
                'product_id' => $productId,
                'buy_price' => round($final_per_price,2),
                'quantity' => $final_aomount
            ];
        }
        echo random_int(1,9999999);
        $character=$data = MongoUtil::query("character", [], ['limit' => 1])[0];
//        var_dump($character);
//        var_dump($final_spend);
        
        if($character['money'] >= $final_spend){
            $character_money=$character['money']-$final_spend;
            $character['money']=round($character_money,2);
            $re_bol=MongoUtil::insertOrUpdateById("character", $character);
            $re_bol=MongoUtil::insertOrUpdateById("inventory", $inventory);
            if($re_bol){
                return array("code" => 200, "msg" => "success");
            }
        }else{
            throw new Exception("金钱不足");
        }
    }catch (Exception $e){
        return array("code" => 500, "msg" =>$e->getMessage());
    }
}

function sell ($productId, $amount) {
    
    return array("code" => 200, "msg" => "success", "data" => null);
}

@$func = $_REQUEST['func'];
//@$placeId=$_REQUEST['placeId'];
$result = null;
switch ($func) {
    case 'marketInit':
        $arr1 = market_product_mongo($_REQUEST['placeId']);
        $arr2 = mystock_mongo();
        $result = array("code" => 200, "msg" => "success", "market_products" => $arr1, 'mystock' => $arr2);
        break;
    case 'buy':
        $result = buy($_REQUEST['productId'], $_REQUEST['amount']);
        break;
    case 'sell':
        $result = sell($_REQUEST['productId'], $_REQUEST['amount']);
        break;
    default:
        break;
}

if ($result <> null) {
    echo json_encode($result);
}

?>



