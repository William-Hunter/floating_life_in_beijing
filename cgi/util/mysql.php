<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/2
 * Time: 16:20
 */


static $IP = "192.168.0.254";
static $user = "root";
static $passwd = "WritenGoodCode";
static $DB = "float_left";


function query ($sql) {
    global $IP, $user, $passwd, $DB;
    $mysqli = new mysqli($IP, $user, $passwd, $DB);
//    printf("B\n");
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
//    printf("C\n");
    $result = array();
    /* Select queries return a resultset */
    if ($resultset = $mysqli->query($sql)) {

        while ($row = mysqli_fetch_array($resultset, MYSQLI_ASSOC)) {
            $result[]=$row;
        }
        $resultset->close();
    }
//    printf("D\n");
    mysqli_close($mysqli);
    return $result;
}


?>

