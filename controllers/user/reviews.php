<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
function index()
{
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "Le mie recensioni");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
    $colnames = array(
        "Voto",
        "Commento",
        "Prodotto",
    );
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/table.html");
    foreach ($colnames as $colname) {
        $table->setContent("colname", $colname);
    }
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/recensioni_utente.html");
    $id = $_SESSION['user']['id'];
    $oid = $mysqli->query("SELECT r.id, r.voto, r.commento, r.prodotti_id as prodotto_id, p.nome as nome_prodotto FROM recensioni as r JOIN prodotti as p on p.id=r.prodotti_id WHERE users_id = '$id'");
    do {
        $recensioni = $oid->fetch_assoc();
        if ($recensioni) {
            foreach ($recensioni as $key => $value) {
                $specific_table->setContent($key, $value);
            }
        }
    }while ($recensioni);
    $table->setContent("specific_table", $specific_table->get());
    $body->setContent("content", $table->get());
    $main->setContent("content", $body->get());
    $main->close();
}

function add()
{
    global $mysqli;
    $voto = $_POST['score'];
    $comment = $_POST['comment'];
    $id_prodotto = $_POST['id_prodotto'];
    $id= $_SESSION['user']['id'];
    $mysqli->query("INSERT INTO tdw_ecommerce.recensioni SET voto = '$voto', commento = '$comment', users_id = '$id', prodotti_id = '$id_prodotto'");
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Recensione aggiunta con successo";
    } elseif ($mysqli->affected_rows == 0) {
        $response['warning'] = "Nessuna recensione aggiunta";
    } else {
        $response['error'] = "Errore nell'aggiunta della recensione";
    }
    exit(json_encode($response));

}
function delete(){
    global $mysqli;
    $id =explode('/', $_SERVER['REQUEST_URI'])[2];
    $mysqli->query("DELETE FROM recensioni WHERE id = '$id'");
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Recensione eliminata con successo";
    } elseif ($mysqli->affected_rows == 0) {
        $response['warning'] = "Nessuna recensione eliminata";
    } else {
        $response['error'] = "Errore nell'eliminazione della recensione";
    }
    exit(json_encode($response));
}