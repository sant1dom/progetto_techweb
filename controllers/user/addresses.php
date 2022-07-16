<?php

use JetBrains\PhpStorm\NoReturn;

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

#[NoReturn] function edit(): void
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];

    $indirizzo = $_POST['indirizzo'];
    $citta = $_POST['citta'];
    $cap = $_POST['cap'];
    $provincia = $_POST['provincia'];
    $nazione = $_POST['nazione'];

    $mysqli->query("UPDATE tdw_ecommerce.indirizzi SET indirizzo = '$indirizzo', citta = '$citta', cap = '$cap', provincia = '$provincia', nazione = '$nazione' WHERE id = '$id'");
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Indirizzo modificato con successo";
    } elseif ($mysqli->affected_rows == 0) {
        $response['warning'] = "Nessun dato modificato";
    } else {
        $response['error'] = "Errore nella modifica dell'indirizzo";
    }
    exit(json_encode($response));
}

#[NoReturn] function create(): void
{
    global $mysqli;

    $indirizzo = $_POST['indirizzo'];
    $citta = $_POST['citta'];
    $cap = $_POST['cap'];
    $provincia = $_POST['provincia'];
    $nazione = $_POST['nazione'];

    if ($citta == "" || $cap == "" || $provincia == "" || $nazione == "" || $indirizzo == "") {
        $response['error'] = "Compilare tutti i campi";
    } else if (strlen($cap)  !== 5 || strlen($provincia) !== 2) {
        $response['error'] = "Il CAP deve avere 5 caratteri e la provincia 2";
    } else {
        $user =  $_SESSION['user'];
        $mysqli->query("INSERT INTO tdw_ecommerce.indirizzi SET indirizzo = '$indirizzo', citta = '$citta', cap = '$cap', provincia = '$provincia', nazione = '$nazione', users_id = {$user["id"]}");

        if ($mysqli->affected_rows == 1) {
            $response['success'] = "Indirizzo aggiunto con successo";
            $response['id'] = $mysqli->insert_id;
        } elseif ($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato aggiunto";
        } else {
            $response['error'] = "Errore nell'aggiunta dell'indirizzo";
        }
    }

    exit(json_encode($response));
}

#[NoReturn] function delete(): void
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];

    $mysqli->query("UPDATE tdw_ecommerce.indirizzi SET valido = 0 WHERE id = '$id'");
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Indirizzo eliminato con successo";
    } elseif ($mysqli->affected_rows == 0) {
        $response['warning'] = "Nessun dato eliminato";
    } else {
        $response['error'] = "Errore nell'eliminazione dell'indirizzo";
    }
    exit(json_encode($response));
}