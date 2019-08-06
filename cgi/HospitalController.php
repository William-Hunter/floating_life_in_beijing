<?php

require('util/MongoUtil.php');
require('util/Calc.php');

function show() {
    try {
        return array(
            "code" => 200, "msg" => "success",
            "data" => MongoUtil::query('surgery')
        );
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}

function deal($surgeryId) {
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
    case 'show':
        $result = show();
        break;
    case 'deal':
        $result = deal($_REQUEST['surgeryId']);
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
