<?php

use JetBrains\PhpStorm\NoReturn;

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

#[NoReturn] function add(): void
{
    global $mysqli;

    $voto = $_POST['score'];
    $comment = $_POST['comment'];
    $id_prodotto = $_POST['id_prodotto'];

    $id = $_SESSION['user']['id'];

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

#[NoReturn] function delete(): void
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
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