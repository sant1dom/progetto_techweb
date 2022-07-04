<?php


require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index()
{
    global $mysqli;
    $colnames = array(
        "Ragione Sociale",
        "Partita IVA",
        "Provenienza",
        "Telefono",
        "Email",
        "Sito Web"
    );

    $oid = $mysqli->query("SELECT id, ragione_sociale, partita_iva, provenienza, telefono, email, sito_web FROM tdw_ecommerce.produttori");
    $main = setupMain();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
    // Riempimento della tabella
    $table->setContent("title", "Produttori");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $produttori_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/produttori.html");
    do {
        $produttori = $oid->fetch_assoc();
        if ($produttori) {
            foreach ($produttori as $key => $value) {
                $produttori_table->setContent($key, $value);
            }
        }
    } while ($produttori);
    $table->setContent("sptable", $produttori_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}

function show()
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $produttore = $mysqli->query("SELECT * FROM tdw_ecommerce.produttori WHERE id = $id");
    if ($produttore->num_rows == 0) {
        header("Location: /admin/producers");
    } else {
        $produttore = $produttore->fetch_assoc();
        $main = setupMain();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/producers/show.html");
        foreach ($produttore as $key => $value) {
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
    $mysqli->query("DELETE FROM tdw_ecommerce.produttori WHERE id = $id AND id NOT IN (SELECT produttori_id FROM tdw_ecommerce.prodotti)");
    $response = array();
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Produttore eliminato con successo";
    } else {
        $response['error'] = "Impossibile cancellare un produttore con prodotti associati";
    }
    exit(json_encode($response));
}

function edit()
{
    global $mysqli;
    $id = $_POST["id"];
    $ragione_sociale = $_POST["ragione_sociale"];
    $partita_iva = $_POST["partita_iva"];
    $provenienza = $_POST["provenienza"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];
    $sito_web = $_POST["sito_web"];
    $response = array();
    if ($id != "" && $ragione_sociale != "" && $partita_iva != "" && $provenienza != '' && $telefono != "" && $email != "" && $sito_web != "") {
        $mysqli->query("UPDATE tdw_ecommerce.produttori SET ragione_sociale = '$ragione_sociale', partita_iva = '$partita_iva', provenienza = '$provenienza', telefono = '$telefono', email = '$email', sito_web = '$sito_web'  WHERE id = $id");
        if ($mysqli->affected_rows == 1) {
            $response['success'] = "Produttore modificato con successo";
        } elseif ($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica del produttore";
        }
    } else {
        $response['error'] = "Errore nella modifica del produttore";
    }
    exit(json_encode($response));
}

function create()
{
    global $mysqli;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ragione_sociale = $_POST["ragione_sociale"];
        $partita_iva = $_POST["partita_iva"];
        $provenienza = $_POST["provenienza"];
        $telefono = $_POST["telefono"];
        $email = $_POST["email"];
        $sito_web = $_POST["sito_web"];
        $response = array();
        if ($ragione_sociale != "" && $partita_iva != "" && $provenienza != '' && $telefono != "" && $email != "" && $sito_web != "") {
            $mysqli->query("INSERT INTO tdw_ecommerce.produttori (id, ragione_sociale, partita_iva, provenienza, telefono, email, sito_web) VALUES (NULL, '$ragione_sociale', '$partita_iva', '$provenienza', '$telefono', '$email', '$sito_web')");
            if ($mysqli->affected_rows == 1) {
                $response['success'] = "Produttore creato con successo";
            } elseif ($mysqli->affected_rows == 0) {
                $response['warning'] = "Nessun dato modificato";
            } else {
                $response['error'] = "Errore nella creazione del produttore";
            }
        } else {
            $response['error'] = "Errore nella creazione del produttore";
        }
        exit(json_encode($response));
    } else {
        $main = setupMain();
        $create = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/producers/create.html");
        $main->setContent("content", $create->get());
        $main->close();
    }
}
