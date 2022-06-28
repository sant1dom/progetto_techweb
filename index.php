<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/dbms.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/utility.inc.php";
/**
 * Routing page
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$request = $_SERVER['REQUEST_URI'];
const __CONTROLLERS__ = __DIR__.'/controllers/';
global $mysqli;
$query = "SELECT * FROM services where '".$request."' like url ORDER BY LENGTH(url) DESC LIMIT 1;";
$oid = $mysqli->query($query);
if($oid->num_rows > 0){
    $oid = $oid->fetch_assoc();
    $controller = $oid['script'];
    $action = $oid['callback'];
    $controller = __CONTROLLERS__.$controller;
    require $controller;
    $action();
}else{
    require __CONTROLLERS__.'errors.php';
}
