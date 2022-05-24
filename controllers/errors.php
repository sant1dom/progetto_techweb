<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
//require "include/dbms.inc.php";
//global $mysqli;

//TODO: parametrizzare i messaggi di errore
$error = "404";
$title = "Pagina non trovata";
$description = "La pagina che stai cercando non esiste";

$main = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/components/main.html");
$body = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/error.html");
/*$oid = $mysqli->query("");
if(!$oid) {
    echo "Errore nella query: ".$mysqli->error;
    exit;
}
*/
$data = array(
    "error" => $error,
    "title" => $title,
    "description" => $description,
);

foreach ($data as $key => $value) {
    $body->setContent($key,$value);
}

$main->setContent("header", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/components/header.html"))->get());
$main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/components/footer.html"))->get());
$main->setContent("content",$body->get());
$main->close();
