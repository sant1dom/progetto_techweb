<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
//require "include/dbms.inc.php";
//global $mysqli;

$main = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/components/main.html");
$body = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/products/show.html");
/*$oid = $mysqli->query("");
if(!$oid) {
    echo "Errore nella query: ".$mysqli->error;
    exit;
}
*/
$title = "Nome del prodotto";
$data = array(
    "image" => "/skins/wizym/image/homepage77.png",
    "name" => "Vino Rosso",
    "price" => "19.99",
);

foreach ($data as $key => $value) {
    $body->setContent($key,$value);
}

$main->setContent("title", $title);
$main->setContent("header", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/components/header.html"))->get());
$main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/components/footer.html"))->get());
$main->setContent("content",$body->get());
$main->close();
