<?php
require $_SERVER['DOCUMENT_ROOT']."/include/template2.inc.php";
//require "include/dbms.inc.php";

$main = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/index.html");
$main->setContent("title","Wine Boutique");
$main->close();
echo "prova";
?>