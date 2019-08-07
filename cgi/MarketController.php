<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/2
 * Time: 17:18
 */


//require_once('util/mysql.php');
require('util/MongoUtil.php');
require('util/Calc.php');
header("content-type", "application/json;charset=UTF-8");


function market_product_mongo($placeId) {
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
    try {
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
        if ($stocks == null) return $results;
        foreach ($stocks as $stock) {
            $stock_name = $stock['stock'][0]->name;
            $stock['name'] = $stock_name;
            unset($stock['stock']);
            $results[] = $stock;
        }
        return $results;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
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
        $final_amount = null;                              //总数量
        $buy_spend = ($product['current_price'] * $amount);  //采购花费
        $final_spend = null;                                  //总花费
        $filter = ['product_id' => $product['_id']];
        $options = ['limit' => 1];
        $rows = MongoUtil::query('inventory', $filter, $options);
        $inventory = null;
        if ($rows <> null) {
            $inventory = $rows[0];
            $final_amount = $amount + $inventory['quantity'];
            $final_spend = ($inventory['buy_price'] * $inventory['quantity']) + $buy_spend;
            $new_price = $final_spend / $final_amount;
            $inventory = [
                '_id' => $inventory['_id'],
                'product_id' => $inventory['product_id'],
                'buy_price' => round($new_price, 2),
                'quantity' => $final_amount
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
        if ($character['money'] < $buy_spend) {
            throw new Exception("金钱不足");
        } elseif ($character['stock'] < (Calc::myStockAmount() + $amount)) {
            throw new Exception("空间不足");
        } else {
            $character_money = $character['money'] - $buy_spend;
            $character['money'] = round($character_money, 2);
            $re_bol = MongoUtil::insertOrUpdateById("character", $character);
            $re_bol = MongoUtil::insertOrUpdateById("inventory", $inventory);
            Calc::evilRise($buy_spend);
            if ($re_bol) {
                return array("code" => 200, "msg" => "success", "cost" => $buy_spend, "money" => $character['money']);
            }
        }
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}

function sell($goodsId, $amount) {
    try {
        //TODO 查询库存
        $inventory = MongoUtil::queryById('inventory', $goodsId);
        $product = MongoUtil::queryById('product', $inventory['product_id']);
        //TODO 验证数量
        if ($amount > $inventory['quantity']) {
            return array("code" => 500, "msg" => "库存数量不足");
        } else {
            //TODO 核算收入
            $earn_money = $product['current_price'] * $amount;
            $character = $data = MongoUtil::query("character", [], ['limit' => 1])[0];
            $character_money = $character['money'] + $earn_money;
            $character['money'] = round($character_money, 2);
            $re_bol = MongoUtil::insertOrUpdateById("character", $character);
            Calc::evilRise($earn_money);
            //TODO 核算剩余库存
            $quantity = $inventory['quantity'];
            $inventory['quantity'] = $quantity - $amount;
            if ($inventory['quantity'] <= 0) {
                $re_bol = MongoUtil::deleteById("inventory", $inventory['_id']);
            } else {
                $re_bol = MongoUtil::insertOrUpdateById("inventory", $inventory);
            }
        }
        $earn = $amount * ($product['current_price'] - $inventory['buy_price']);
        return array("code" => 200, "msg" => "success", "earn" => round($earn, 2), "money" => $character['money']);
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }

}


function afterDay($placeId) {
    $state = MongoUtil::query("character", [], ['limit' => 1])[0];
    $new_day = $state['date'] + 1;
    $state['date'] = (int)$new_day;
    $event = [];

    //健康损失随机事件
    if (random_int(1, 10) > 7) {         //事件发生的机率
        $randomHurt = random_int(1, 20);
        $new_hurt=$state['health'] - $randomHurt;           //随机的伤害值
        $state['health']=$new_hurt<0?0:$new_hurt;           //不低于0
        $event[] =  '我被打了，损失' . $randomHurt . '点健康';
    }

    //利率波动
    $state['interest'] = Calc::rateChange();
    $event[] = '今天的利率是' . ($state['interest'] * 100) . '%';

    //日子过去一天了，债务也在涨
    $debt = ($state['interest'] + 1) * $state['debt'];
    $state['debt'] = round($debt, 2);
    MongoUtil::insertOrUpdateById('character', $state);

    //价格波动
    $product_list = MongoUtil::query("product", [], null);
    foreach ($product_list as $product) {
        $new_price = $product['base_price'] * (random_int(1, 19) / 10.0);
        $product['current_price'] = round($new_price, 2);
        MongoUtil::insertOrUpdateById('product', $product);
    }

//  地区商品波动
    MongoUtil::delete('product_of_place', ['place_id' => $placeId]);
    $place = MongoUtil::queryById('place', $placeId);
    $products = MongoUtil::query('product');
    for ($index = 0; $index < count($products) / 4; $index++) {               //操作商品总数的的4分之1次
        $remove_index = random_int(0, (count($products) - 1) * 2);         //每次有50%可能性，去掉一个随机商品
        array_splice($products, $remove_index, 1);       //去掉一个商品
    }
    foreach ($products as $product) {                                //循环插入地区的商品
        MongoUtil::insert('product_of_place', [
            '_id' => strval(random_int(1, 9999999)),
            'product_id' => $product['_id'],
            'place_id' => $place['_id']
        ]);
    }
    return array("code" => 200, "msg" => "success", "event" => $event);
}


@$func = $_REQUEST['func'];
$result = null;
switch ($func) {
    case 'marketInit':
        $result = afterDay($_REQUEST['placeId']);
        break;
    case 'marketInfo':
        $arr1 = market_product_mongo($_REQUEST['placeId']);
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



