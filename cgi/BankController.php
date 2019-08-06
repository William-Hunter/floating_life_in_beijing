<?php

require('util/MongoUtil.php');
require('util/Calc.php');

function init() {
    try {
        $state = Calc::mystate();
        $randomRate=random_int(0,100)/100;
        $state["interest"] = $randomRate;
        MongoUtil::insertOrUpdateById('character', $state);
        return array(
            "code" => 200, "msg" => "success",
            "rate" => $randomRate,"debt"=>$state["debt"]
        );
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}

function borrow($surgeryId) {
    try {
        $state = Calc::mystate();
        $surgery = MongoUtil::queryById('surgery', $surgeryId);
        if ($state['money'] < $surgery['price']) {
            throw new Exception("你没有足够钱支付手术费");
        }
        $newHeal=$state['health'] + $surgery['heal'];
        $state["health"] = (int)($newHeal>100?100:$newHeal);
        $newPrice = $state['money'] - $surgery['price'];
        $state["money"] = $newPrice;
        MongoUtil::insertOrUpdateById('character', $state);
        return array(
            "code" => 200,
            "msg" => 'success',
            "cost" => $surgery['price'],
            "money" => $state['money']
        );
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}


function repay($surgeryId) {
    try {
        $state = Calc::mystate();
        $surgery = MongoUtil::queryById('surgery', $surgeryId);
        if ($state['money'] < $surgery['price']) {
            throw new Exception("你没有足够钱支付手术费");
        }
        $newHeal=$state['health'] + $surgery['heal'];
        $state["health"] = (int)($newHeal>100?100:$newHeal);
        $newPrice = $state['money'] - $surgery['price'];
        $state["money"] = $newPrice;
        MongoUtil::insertOrUpdateById('character', $state);
        return array(
            "code" => 200,
            "msg" => 'success',
            "cost" => $surgery['price'],
            "money" => $state['money']
        );
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}

header("content-type", "application/json;charset=UTF-8");
@$func = $_REQUEST['func'];
$result = null;
switch ($func) {
    case 'init':
        $result = init();
        break;
    case 'borrow':
        $result = borrow($_REQUEST['money']);
        break;
    case 'repay':
        $result = repay($_REQUEST['money']);
        break;
    default:
        break;
}
if ($result <> null) {
    echo json_encode($result);
}


?>
