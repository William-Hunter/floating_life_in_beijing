<?php

require('util/MongoUtil.php');
require('util/Calc.php');


function show() {
    try {
        return array("code" => 200, "msg" => "success", "data" => MongoUtil::query('bribery'));
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}

function bribery($houseId) {
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
    case 'bribery':
        $result = bribery($_REQUEST['briberyId']);
        break;
    case 'deal':
        $result = array("code" => 200, "msg" => "success");
        break;
    default:
        break;
}
if ($result <> null) {
    echo json_encode($result);
}
?>
