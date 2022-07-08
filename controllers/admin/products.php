<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index(){
    global $mysqli;
    $colnames = array(
        "Nome",
        "Prezzo",
        "Dimensione",
        "Quantità disponibile",
        "Descrizione"
    );
    $oid = $mysqli->query("SELECT id, nome, prezzo, dimensione, quantita_disponibile, descrizione FROM tdw_ecommerce.prodotti");
    $main = setupMainAdmin();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
    // Riempimento della tabella
    $table->setContent("title", "Prodotti");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $prodotti_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/prodotti.html");
    do {
        $products = $oid->fetch_assoc();
        if ($products) {
            foreach ($products as $key => $value) {
                $prodotti_table->setContent($key, $value);
            }
        }
    } while ($products);
    $table->setContent("sptable", $prodotti_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}
function show(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $prodotto = $mysqli->query("SELECT prodotti.id, prodotti.nome, prezzo, dimensione, quantita_disponibile, descrizione, c.nome as categoria_selected, p.ragione_sociale as produttore_selected, p2.nazione as nazione_selected, p2.regione as regione_selected FROM tdw_ecommerce.prodotti JOIN tdw_ecommerce.categorie c on c.id = prodotti.categorie_id JOIN tdw_ecommerce.produttori p on p.id = prodotti.produttori_id JOIN tdw_ecommerce.provenienze p2 on p2.id = prodotti.provenienze_id WHERE prodotti.id = $id");
    if ($prodotto->num_rows == 0) {
        header("Location: /admin/products");
    } else {
        $prodotto = $prodotto->fetch_assoc();
        $main = setupMainAdmin();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/products/show.html");
        foreach ($prodotto as $key => $value) {
            $show->setContent($key, $value);
        }
        populateSelectProduct($mysqli, $show);
        $show->setContent("categoria_selected", $prodotto['categoria_selected']);
        $show->setContent("produttore_selected", $prodotto['produttore_selected']);
        $show->setContent("nazione_selected", $prodotto['nazione_selected']);
        $show->setContent("regione_selected", $prodotto['regione_selected']);

        $main->setContent("content", $show->get());
        $main->close();
    }
}
function create(){
    global $mysqli;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST["nome"];
        $prezzo = $_POST["prezzo"];
        $dimensione = $_POST["dimensione"];
        $quantita_disponibile = $_POST["quantita_disponibile"];
        $descrizione = $_POST["descrizione"];
        $categoria = $_POST["categoria"];
        $produttore = $_POST["produttore"];
        $provenienza = $_POST["provenienza"];
        $response = array();
        if ($nome !== "" && $prezzo !== "" && $dimensione !== "" && $quantita_disponibile !== "" && $descrizione !== "" && $categoria !== "" && $produttore !== "" && $provenienza !== "") {
            $mysqli->query("INSERT INTO tdw_ecommerce.prodotti (nome, prezzo, dimensione, quantita_disponibile, descrizione, categorie_id, produttori_id, provenienze_id) VALUES ('$nome', $prezzo, '$dimensione', $quantita_disponibile, '$descrizione', $categoria, $produttore, $provenienza)");
            if ($mysqli->affected_rows == 1) {
                $response['success'] = "Prodotto creato con successo";
            } elseif ($mysqli->affected_rows == 0) {
                $response['warning'] = "Nessun dato modificato";
            } else {
                $response['error'] = "Errore nella creazione del prodotto";
            }
        } else {
            $response['error'] = "Errore nella creazione del prodotto";
        }
        exit(json_encode($response));
    } else {
        $main = setupMainAdmin();
        $create = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/offers/create.html");
        populateSelectProduct($mysqli, $create);
        $main->setContent("content", $create->get());
        $main->close();
    }
}

/**
 * @param mysqli $mysqli
 * @param Template $template
 * @return void
 */
function populateSelectProduct(mysqli $mysqli, Template $template): void {
    $oid = $mysqli->query("SELECT id as produttore_id, ragione_sociale as produttore FROM tdw_ecommerce.produttori ORDER BY ragione_sociale");
    do {
        $produttori = $oid->fetch_assoc();
        if ($produttori) {
            foreach ($produttori as $key => $value) {
                $template->setContent($key, $value);
            }
        }
    } while ($produttori);
    $oid = $mysqli->query("SELECT id as categoria_id, nome as categoria FROM tdw_ecommerce.categorie ORDER BY nome");
    do {
        $categorie = $oid->fetch_assoc();
        if ($categorie) {
            foreach ($categorie as $key => $value) {
                $template->setContent($key, $value);
            }
        }
    } while ($categorie);
    $oid = $mysqli->query("SELECT id as provenienza_id, nazione, regione FROM tdw_ecommerce.provenienze ORDER BY nazione, regione");
    do {
        $provenienze = $oid->fetch_assoc();
        if ($provenienze) {
            foreach ($provenienze as $key => $value) {
                $template->setContent($key, $value);
            }
        }
    } while ($provenienze);
}

function edit(){
    global $mysqli;
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $prezzo = $_POST["prezzo"];
    $dimensione = $_POST["dimensione"];
    $quantita_disponibile = $_POST["quantita_disponibile"];
    $descrizione = $_POST["descrizione"];
    $categoria = $_POST["categoria"];
    $produttore = $_POST["produttore"];
    $provenienza = $_POST["provenienza"];
    $response = array();
    if ($id != "" && $nome != "" && $prezzo != "" && $dimensione != "" && $quantita_disponibile != "" && $descrizione != "" && $categoria != "" && $produttore != "" && $provenienza != "") {
        $mysqli->query("UPDATE tdw_ecommerce.prodotti SET nome = '$nome', prezzo = '$prezzo', dimensione = '$dimensione', quantita_disponibile = '$quantita_disponibile', descrizione = '$descrizione', categorie_id = '$categoria', produttori_id = '$produttore', provenienze_id = '$provenienza' WHERE id = '$id'");
        if($mysqli->affected_rows == 1){
            $response['success'] = "Prodotto modificato con successo";
        } elseif($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica del prodotto";
        }
    } else {
        $response['error'] = "Errore nella modifica del prodotto";
    }
    exit(json_encode($response));
}

function delete(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $mysqli->query("DELETE FROM tdw_ecommerce.prodotti WHERE id = $id AND id NOT IN (SELECT prodotti_id FROM tdw_ecommerce.ordini_has_prodotti)");
    $response = array();
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Prodotto eliminato con successo";
    } else {
        $response['error'] = "Impossibile cancellare un prodotto con ordini associati";
    }
    exit(json_encode($response));
}