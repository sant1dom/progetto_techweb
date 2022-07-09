<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index() {
    global $mysqli;
    $colnames = array(
        "Percentuale",
        "Data Inizio",
        "Data Fine",
        "Prodotto",
        "Prezzo"
    );

    $oid = $mysqli->query("SELECT offerte.id, percentuale, data_inizio, data_fine, p.id as prodotto_id, nome as prodotto, prezzo 
                                    FROM tdw_ecommerce.offerte 
                                        JOIN tdw_ecommerce.prodotti p on p.id = offerte.prodotti_id
                                    WHERE data_fine >= NOW()");
    $main = setupMainAdmin();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
    // Riempimento della tabella
    $table->setContent("title", "Offerte");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $offerte_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/offerte.html");
    do {
        $offerte = $oid->fetch_assoc();
        if ($offerte) {
            foreach ($offerte as $key => $value) {
                $offerte_table->setContent($key, $value);
            }
        }
    } while ($offerte);
    $table->setContent("sptable", $offerte_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}

function show(){
    global $mysqli;
    $id = explode("/", $_SERVER['REQUEST_URI'])[3];
    $offerta = $mysqli->query("SELECT offerte.id, percentuale, data_inizio, data_fine, p.id as prodotto_id, nome as prodotto, prezzo 
                                    FROM tdw_ecommerce.offerte 
                                        JOIN tdw_ecommerce.prodotti p on p.id = offerte.prodotti_id
                                    WHERE offerte.id = $id ");
    if ($offerta->num_rows == 0) {
        header("Location: /admin/products");
    } else {
        $offerta = $offerta->fetch_assoc();
        $main = setupMainAdmin();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/offers/show.html");
        foreach ($offerta as $key => $value) {
            $show->setContent($key, $value);
        }
        $main->setContent("content", $show->get());
        $main->close();
    }
}

function edit(){
    global $mysqli;
    $id = $_POST['id'];
    $percentuale = $_POST['percentuale'];
    $data_inizio = $_POST['data_inizio'];
    $data_fine = $_POST['data_fine'];

    $response = array();
    if($id != "" && $percentuale != "" && $data_inizio != "" && $data_fine != ""){
        $mysqli->query("UPDATE tdw_ecommerce.offerte SET percentuale = $percentuale, data_inizio = '$data_inizio', data_fine = '$data_fine' WHERE id = $id");
        if($mysqli->affected_rows == 1){
            $response['success'] = "Offerta modificata con successo";
        } else {
            $response['error'] = "Errore nella modifica dell'offerta";
        }
    } else {
        $response['error'] = "Errore nella modifica dell'offerta";
    }
    exit(json_encode($response));
}

function delete(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $mysqli->query("DELETE FROM tdw_ecommerce.offerte WHERE id = $id");
    $response = array();
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Offerta eliminata con successo";
    } else {
        $response['error'] = "Impossibile cancellare l'offerta";
    }
    exit(json_encode($response));
}

function create(){
    global $mysqli;
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $percentuale = $_POST['percentuale'];
        $data_inizio = $_POST['data_inizio'];
        $data_fine = $_POST['data_fine'];
        $prodotto = $_POST['prodotto'];
        $response = array();
        if($percentuale != "" && $data_inizio != "" && $data_fine != "" && $prodotto != ""){
            $mysqli->query("INSERT INTO tdw_ecommerce.offerte (percentuale, data_inizio, data_fine, prodotti_id) VALUES ($percentuale, '$data_inizio', '$data_fine', $prodotto)");
            if($mysqli->affected_rows == 1){
                $response['success'] = "Offerta creata con successo";
            } else {
                $response['error'] = "Errore nella creazione dell'offerta";
            }
        } else {
            $response['error'] = "Errore nella creazione dell'offerta";
        }
        exit(json_encode($response));
    } else {
        $main = setupMainAdmin();
        $create = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/offers/create.html");
        $oid = $mysqli->query("SELECT id, nome, prezzo FROM tdw_ecommerce.prodotti WHERE quantita_disponibile > 0 ORDER BY nome");
        do {
            $prodotti = $oid->fetch_assoc();
            if ($prodotti) {
                foreach ($prodotti as $key => $value) {
                    $create->setContent($key, $value);
                }
            }
        } while ($prodotti);
        $main->setContent("content", $create->get());
        $main->close();
    }

}