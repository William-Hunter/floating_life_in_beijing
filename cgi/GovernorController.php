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

function buyBribery($briberyId) {
    try {
        $state = Calc::mystate();
        $bribery = MongoUtil::queryById('bribery', $briberyId);
        if ($state['money'] < $bribery['price']) {
            throw new Exception("你没有足够钱去行贿");
        }
        $newCrime = $state['crime'] - $bribery['washout'];
        $state["crime"] = (int)$newCrime<0?0:$newCrime;
        $newPrice = $state['money'] - $bribery['price'];
        $state["money"] = $newPrice;
        MongoUtil::insertOrUpdateById('character', $state);
        return array(
            "code" => 200,
            "msg" => 'success',
            "cost" => $bribery['price'],
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
    case 'bribery':
        $result = buyBribery($_REQUEST['briberyId']);
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
