<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index(){
    global $mysqli;
    $colnames = array(
        "Nome",
        "Cognome",
        "Email",
        "Telefono"
    );

    $oid = $mysqli->query("SELECT id, nome, cognome, email, telefono FROM tdw_ecommerce.users");
    $main = setupMain();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
        // Riempimento della tabella
    $table->setContent("title", "Utenti");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $utenti_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/utenti.html");
    do {
        $users = $oid->fetch_assoc();
        if ($users) {
            foreach ($users as $key => $value) {
                $utenti_table->setContent($key, $value);
            }
        }
    } while ($users);
    $table->setContent("sptable", $utenti_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}

function show(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $utente = $mysqli->query("SELECT * FROM tdw_ecommerce.users WHERE id = $id");
    if ($utente->num_rows == 0) {
        header("Location: /admin/users");
    } else {
        $utente = $utente->fetch_assoc();
        $main = setupMain();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/users/show.html");
        foreach ($utente as $key => $value) {
            $show->setContent($key, $value);
        }
        $main->setContent("content", $show->get());
        $main->close();
    }
}

// Per il momento non funziona dobbiamo parlare di cosa succede se cancelliamo un utente
function delete(){
    global $mysqli;
    echo "DELETE";
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $mysqli->query("DELETE FROM tdw_ecommerce.users WHERE id = $id");
    $response = array();
    if($mysqli->affected_rows == 1){
        $response['success'] = "Utente eliminato con successo";
    } else {
        $response['error'] = "Errore nell'eliminazione dell'utente";
    }
    exit(json_encode($response));
}

function edit(){
    global $mysqli;
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];
    $response = array();
    if ($id != "" && $nome != "" && $cognome != "" && $email != "" && $telefono != "") {
        $mysqli->query("UPDATE tdw_ecommerce.users SET nome = '$nome', cognome = '$cognome', email = '$email', telefono = '$telefono' WHERE id = $id");
        if($mysqli->affected_rows == 1){
            $response['success'] = "Utente modificato con successo";
        } elseif($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica dell'utente";
        }
    } else {
        $response['error'] = "Errore nella modifica dell'utente";
    }
    exit(json_encode($response));
}