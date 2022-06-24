<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
require $_SERVER['DOCUMENT_ROOT'] . "/include/dbms.inc.php";
global $mysqli;

$colnames = array(
    "Nome",
    "Cognome",
    "Email",
    "Telefono"
);

$oid = $mysqli->query("SELECT id, nome, cognome, email, telefono FROM tdw_ecommerce.users");


$main = new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/views/main.html");
// Default set delle parti statiche
$main->setContent("header", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/header.html"))->get());
$main->setContent("sidebar", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/sidebar.html"))->get());
$main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/footer.html"))->get());

// Creazione del contenuto
$crud = new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/views/crud.html");
$table = new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/table.html");
// Riempimento della tabella
$table->setContent("title", "Utenti");
foreach ($colnames as $value) {
    $table->setContent("colname", $value);
}
$utenti_table = new Template($_SERVER['DOCUMENT_ROOT']."/skins/admin/sash/dtml/components/specific_tables/utenti.html");
do{
    $users = $oid->fetch_assoc();
    if($users){
        foreach ($users as $key => $value) {
            $utenti_table->setContent($key, $value);
        }
    }
} while ($users);
$table->setContent("sptable", $utenti_table->get());
$crud->setContent("table", $table->get());
$main->setContent("content", $crud->get());
$main->close();