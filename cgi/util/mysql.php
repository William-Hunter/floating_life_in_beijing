<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/2
 * Time: 16:20
 */


static $IP = "118.25.212.166";
static $user = "root";
static $passwd = "WeAreHacker2019;;";
static $DB = "float_left";
static $port = 3304;

function query ($sql) {
    global $IP, $user, $passwd, $DB,$port;
    $mysqli = new mysqli($IP, $user, $passwd, $DB,$port);
//    printf("B\n");
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    $result = array();
    /* Select queries return a resultset */
    if ($resultset = $mysqli->query($sql)) {
        while ($row = mysqli_fetch_array($resultset, MYSQLI_ASSOC)) {
            $result[]=$row;
        }
        $resultset->close();
    }
    mysqli_close($mysqli);
    return $result;
}


?>

