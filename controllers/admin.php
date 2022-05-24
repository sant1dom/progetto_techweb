<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

$colnames = array(
    "Data",
    "Stato",
    "Totale",
    "Cliente"
);

$ordini = array(
    "id" => 1,
    "data" => "20-03-2022",
    "stato" => "Consegnato",
    "totale" => "22.30$",
    "utente" => "Cliente"
);

$main = new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/main.html");
// Default set delle parti statiche
$main->setContent("header", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/header.html"))->get());
$main->setContent("sidebar", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/sidebar.html"))->get());
$main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/footer.html"))->get());
// Creazione del contenuto
$home = new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/home.html");
$table = new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/table.html");
// Riempimento della tabella
foreach ($colnames as $value) {
    $table->setContent("colname", $value);
}
foreach ($ordini as $key => $value) {
    $table->setContent($key, $value);
}
$home->setContent("table", $table->get());
$main->setContent("content",$home->get());
$main->close();
