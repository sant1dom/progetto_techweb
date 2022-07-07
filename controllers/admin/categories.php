<?php

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index()
{
    global $mysqli;
    $colnames = array(
        "Nome"
    );

    $oid = $mysqli->query("SELECT id, nome FROM tdw_ecommerce.categorie");
    $main = setupMainAdmin();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
    // Riempimento della tabella
    $table->setContent("title", "Categorie");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $categorie_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/categorie.html");
    do {
        $categorie = $oid->fetch_assoc();
        if ($categorie) {
            foreach ($categorie as $key => $value) {
                $categorie_table->setContent($key, $value);
            }
        }
    } while ($categorie);
    $table->setContent("sptable", $categorie_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}

function show()
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $categoria = $mysqli->query("SELECT * FROM tdw_ecommerce.categorie WHERE id = $id");
    if ($categoria->num_rows == 0) {
        header("Location: /admin/categories");
    } else {
        $categoria = $categoria->fetch_assoc();
        $main = setupMainAdmin();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/categories/show.html");
        foreach ($categoria as $key => $value) {
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
    $mysqli->query("DELETE FROM tdw_ecommerce.categorie WHERE id = $id AND id NOT IN (SELECT categorie_id FROM tdw_ecommerce.prodotti)");
    $response = array();
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Categoria eliminata con successo";
    } else {
        $response['error'] = "Impossibile cancellare una categoria con prodotti associati";
    }
    exit(json_encode($response));
}

function edit()
{
    global $mysqli;
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $response = array();
    if ($id != "" && $nome != "") {
        $mysqli->query("UPDATE tdw_ecommerce.categorie SET nome = '$nome' WHERE id = $id");
        if ($mysqli->affected_rows == 1) {
            $response['success'] = "Categoria modificata con successo";
        } elseif ($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica della categoria";
        }
    } else {
        $response['error'] = "Errore nella modifica della categoria";
    }
    exit(json_encode($response));
}

function create(){
    global $mysqli;
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $nome = $_POST["nome"];
        $response = array();
        if ($nome != "") {
            $mysqli->query("INSERT INTO tdw_ecommerce.categorie (id, nome) VALUES (NULL, '".$nome."')");
            if ($mysqli->affected_rows == 1) {
                $response['success'] = "Categoria creata con successo";
            } elseif ($mysqli->affected_rows == 0) {
                $response['warning'] = "Nessun dato modificato";
            } else {
                $response['error'] = "Errore nella creazione della categoria";
            }
        } else {
            $response['error'] = "Errore nella creazione della categoria";
        }
        exit(json_encode($response));
    } else {
        $main = setupMainAdmin();
        $create = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/categories/create.html");
        $main->setContent("content", $create->get());
        $main->close();
    }
}