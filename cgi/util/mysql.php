<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/2
 * Time: 16:20
 */

require_once('config.php');

function query ($sql) {
    global $DB_IP, $DB_user, $DB_passwd, $DB_name,$DB_port;
    $mysqli = new mysqli($DB_IP, $DB_user, $DB_passwd, $DB_name,$DB_port);
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

