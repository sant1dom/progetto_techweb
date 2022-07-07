<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
//require "include/dbms.inc.php";
//global $mysqli;

$main = setupMainWizym();
$body = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/contacts.html");
/*$oid = $mysqli->query("");
if(!$oid) {
    echo "Errore nella query: ".$mysqli->error;
    exit;
}
*/
$data = array(
    "image" => "/skins/wizym/image/homepage77.png",
    "name" => "Vino Rosso",
    "price" => "19.99",
);

foreach ($data as $key => $value) {
    $body->setContent($key,$value);
}

$main->setContent("title", "CONTACT US");
$main->setContent("content",$body->get());
$main->close();
