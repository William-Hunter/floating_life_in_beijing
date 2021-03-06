<?php
require('util/MongoUtil.php');
require('util/Calc.php');


/**
 * 返回所有的房子
 * @return array
 * @throws \MongoDB\Driver\Exception\Exception
 */
function show() {
    try {
        return array("code" => 200, "msg" => "success", "data" => MongoUtil::query('house'));
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}

function buy($houseId) {
    try {
        $state = Calc::mystate();
        $house = MongoUtil::queryById('house', $houseId);
        if ($state['money'] < $house['price']) {
            throw new Exception("你没有足够钱购买这个房产");
        }
        $newStock = $state['stock'] + $house['stock'];
        $state["stock"] = (int)$newStock;
        $newPrice = $state['money'] - $house['price'];
        $state["money"] = $newPrice;
        MongoUtil::insertOrUpdateById('character', $state);
        return array("code" => 200, "msg" => 'success', "cost" => $house['price'], "money" => $state['money']);
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}


header("content-type", "application/json;charset=UTF-8");
@$func = $_REQUEST['func'];
$result = null;
switch ($func) {
    case 'show':
        $result = show();
        break;
    case 'buy':
        $result = buy($_REQUEST['houseId']);
        break;
    default:
        break;
}
if ($result <> null) {
    echo json_encode($result);
}
?>
