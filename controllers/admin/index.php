<?php
    require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function admin(){
    $colnames = array(
        "Data",
        "Stato",
        "Totale",
        "Cliente"
    );

    $orders = array(
        "id" => 1,
        "data" => "20-03-2022",
        "stato" => "Consegnato",
        "totale" => "22.30$",
        "utente" => "Cliente"
    );

    $main = setupMain();
// Creazione del contenuto
    $home = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/home.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
    $ordini = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/ordini.html");
// Riempimento della tabella
    $ordini->setContent("title", "Ultimi Ordini");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    foreach ($orders as $key => $value) {
        $ordini->setContent($key, $value);
    }
    $table->setContent("sptable", $ordini->get());
    $home->setContent("table", $table->get());
    $main->setContent("content", $home->get());
    $main->close();
}
