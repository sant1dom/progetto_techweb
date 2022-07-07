<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
//require "include/dbms.inc.php";
//global $mysqli;

$main = setupMainUser();
$body = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/cart.html");
/*$oid = $mysqli->query("");
if(!$oid) {
    echo "Errore nella query: ".$mysqli->error;
    exit;
}
*/
$data = array(

);

foreach ($data as $key => $value) {
    $body->setContent($key,$value);
}

$main->setContent("title", "CART");
$main->setContent("content",$body->get());
$main->close();
