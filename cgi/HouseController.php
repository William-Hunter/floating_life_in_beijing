<?php
require('util/MongoUtil.php');
require('util/Calc.php');


/**
 * 返回所有的房子
 * @return array
 * @throws \MongoDB\Driver\Exception\Exception
 */
function show(){
    return array("code" => 200, "msg" => "success","data"=>MongoUtil::query('house'));
}


header("content-type", "application/json;charset=UTF-8");
@$func = $_REQUEST['func'];
$result = null;
switch ($func) {
    case 'show':
        $result =show();
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
