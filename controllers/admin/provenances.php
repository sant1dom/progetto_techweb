<?php

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index()
{
    global $mysqli;
    $colnames = array(
        "Nazione",
        "Regione"
    );

    $oid = $mysqli->query("SELECT id, nazione, regione FROM tdw_ecommerce.provenienze");
    $main = setupMainAdmin();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
    // Riempimento della tabella
    $table->setContent("title", "Provenienze");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $provenienze_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/provenienze.html");
    do {
        $provenienze = $oid->fetch_assoc();
        if ($provenienze) {
            foreach ($provenienze as $key => $value) {
                $provenienze_table->setContent($key, $value);
            }
        }
    } while ($provenienze);
    $table->setContent("sptable", $provenienze_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}

function show()
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $provenienza = $mysqli->query("SELECT * FROM tdw_ecommerce.provenienze WHERE id = $id");
    if ($provenienza->num_rows == 0) {
        header("Location: /admin/provenances");
    } else {
        $provenienza = $provenienza->fetch_assoc();
        $main = setupMainAdmin();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/provenances/show.html");
        foreach ($provenienza as $key => $value) {
            $show->setContent($key, $value);
        }
        $main->setContent("content", $show->get());
        $main->close();
    }
}

function delete()
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $mysqli->query("DELETE FROM tdw_ecommerce.provenienze WHERE id = $id AND id NOT IN (SELECT provenienze_id FROM tdw_ecommerce.prodotti)");
    $response = array();
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Provenienza eliminata con successo";
    } else {
        $response['error'] = "Impossibile cancellare una provenienza con prodotti associati";
    }
    exit(json_encode($response));
}

function edit()
{
    global $mysqli;
    $id = $_POST["id"];
    $nazione = $_POST["nazione"];
    $regione = $_POST["regione"];
    $response = array();
    if ($id != "" && $nazione != "" && $regione != "") {
        $mysqli->query("UPDATE tdw_ecommerce.provenienze SET nazione = '$nazione', regione = '$regione' WHERE id = $id");
        if ($mysqli->affected_rows == 1) {
            $response['success'] = "Provenienza modificata con successo";
        } elseif ($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica della provenienza";
        }
    } else {
        $response['error'] = "Errore nella modifica della provenienza";
    }
    exit(json_encode($response));
}

function create(){
    global $mysqli;
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $nazione = $_POST["nazione"];
        $regione = $_POST["regione"];
        $response = array();
        if ($nazione != "") {
            $mysqli->query("INSERT INTO tdw_ecommerce.provenienze (id, nazione, regione) VALUES (NULL, '$nazione', '$regione')");
            if ($mysqli->affected_rows == 1) {
                $response['success'] = "Provenienza creata con successo";
            } elseif ($mysqli->affected_rows == 0) {
                $response['warning'] = "Nessun dato modificato";
            } else {
                $response['error'] = "Errore nella creazione della provenienza";
            }
        } else {
            $response['error'] = "Errore nella creazione della provenienza";
        }
        exit(json_encode($response));
    } else {
        $main = setupMainAdmin();
        $create = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/provenances/create.html");
        $main->setContent("content", $create->get());
        $main->close();
    }
}