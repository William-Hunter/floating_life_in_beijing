<?php

require('util/MongoUtil.php');
require('util/Calc.php');

function init() {
    try {
        $state=Calc::rateChange();
        return array(
            "code" => 200, "msg" => "success",
            "rate" => $state['interest'],"debt"=>$state["debt"]
        );
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}

function borrow($money) {
    try {
        $state = Calc::mystate();
        if ($state['money'] < $money) {
            throw new Exception("你没有足够钱去还钱");
        }
        $newMoney = $state['money'] + $money;
        $state["money"] = $newMoney;
        $newDebt = $state['debt'] + $money;
        $state["debt"] = $newDebt;
        MongoUtil::insertOrUpdateById('character', $state);
        return array(
            "code" => 200,
            "msg" => 'success',
            "cost" => $money,
            "money" => $state['money']
        );
    } catch (Exception $e) {
        return array("code" => 500, "msg" => $e->getMessage());
    }
}


function repay($pay_money) {
    try {
        $state = Calc::mystate();
        $pay_money=$pay_money>$state['debt']?$state['debt']:$pay_money;
        $newMoney = $state['money'] - $pay_money;
        $state["money"] = $newMoney;
        $newDebt = $state['debt'] - $pay_money;
        $state["debt"] = $newDebt;
        MongoUtil::insertOrUpdateById('character', $state);
        return array(
            "code" => 200,
            "msg" => 'success',
            "cost" => $pay_money,
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
