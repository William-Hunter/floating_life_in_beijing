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


function market_product_mongo($placeId,$arrived) {
    if($arrived<>null){

    }
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

function mystock_mongo() {
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
function buy($productId, $amount) {
    try {
        $product = MongoUtil::queryById('product', $productId);
        $new_price = null;                                  //新均价
        $final_aomount = null;                              //总数量
        $buy_spend = ($product['current_price'] * $amount);  //采购花费
        $final_spend = null;                                  //总花费
        $filter = ['product_id' => $product['_id']];
        $options = ['limit' => 1];
        $rows = MongoUtil::query('inventory', $filter, $options);
        $inventory = null;
        if ($rows <> null) {
            $inventory = $rows[0];
            $final_aomount = $amount + $inventory['quantity'];
            $final_spend = ($inventory['buy_price'] * $inventory['quantity']) + $buy_spend;
            $new_price = $final_spend / $final_aomount;
            $inventory = [
                '_id' => $inventory['_id'],
                'product_id' => $inventory['product_id'],
                'buy_price' => round($new_price, 2),
                'quantity' => $final_aomount
            ];
        } else {
            $inventory = [
                '_id' => strval(random_int(1, 9999999)),
                'product_id' => $productId,
                'buy_price' => round($product['current_price'], 2),
                'quantity' => (int)$amount
            ];
        }
        $character = $data = MongoUtil::query("character", [], ['limit' => 1])[0];
        if ($character['money'] >= $buy_spend) {
            $character_money = $character['money'] - $buy_spend;
            $character['money'] = round($character_money, 2);
            $re_bol = MongoUtil::insertOrUpdateById("character", $character);
            $re_bol = MongoUtil::insertOrUpdateById("inventory", $inventory);
            if ($re_bol) {
                return array("code" => 200, "msg" => "success");
            }
        } else {
            throw new Exception("金钱不足");
        }
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}

function sell($goodsId, $amount) {
    //TODO 查询库存
    $inventory = MongoUtil::queryById('inventory', $goodsId);
    //TODO 验证数量
    if ($amount > $inventory['quantity']) {
        return array("code" => 500, "msg" => "库存数量不足");
    } else {
        //TODO 核算收入
        $product = MongoUtil::queryById('product', $inventory['product_id']);
        $earn_money = $product['current_price'] * $amount;
        $character = $data = MongoUtil::query("character", [], ['limit' => 1])[0];
        $character_money = $character['money'] + $earn_money;
        $character['money'] = round($character_money, 2);
        $re_bol = MongoUtil::insertOrUpdateById("character", $character);
        //TODO 核算剩余库存
        $quantity = $inventory['quantity'];
        $inventory['quantity'] = $quantity - $amount;
        if ($inventory['quantity'] <= 0) {
            $re_bol = MongoUtil::deleteById("inventory", $inventory['_id']);
        } else {
            $re_bol = MongoUtil::insertOrUpdateById("inventory", $inventory);
        }
    }
    return array("code" => 200, "msg" => "success");
}

@$func = $_REQUEST['func'];
//@$placeId=$_REQUEST['placeId'];
$result = null;
switch ($func) {
    case 'marketInit':
        $arr1 = market_product_mongo($_REQUEST['placeId'],$_REQUEST['arrived']);
        $arr2 = mystock_mongo();
        $result = array("code" => 200, "msg" => "success", "market_products" => $arr1, 'mystock' => $arr2);
        break;
    case 'buy':
        $result = buy($_REQUEST['productId'], $_REQUEST['amount']);
        break;
    case 'sell':
        $result = sell($_REQUEST['goodsId'], $_REQUEST['amount']);
        break;
    default:
        break;
}

if ($result <> null) {
    echo json_encode($result);
}

?>



