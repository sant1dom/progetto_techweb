<?php

use JetBrains\PhpStorm\NoReturn;

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function show(): void
{
    global $mysqli;
    $main = setupMainUser();
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    if ($id != 0) {
        $main->setContent("title", "Modifica indirizzo");
        $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/addresses/form_indirizzo.html");
        $email = $_SESSION['user']['email'];
        $oid = $mysqli->query("SELECT id, indirizzo, citta, cap, provincia,nazione FROM tdw_ecommerce.indirizzi WHERE id = '$id'");
        do {
            $indirizzo = $oid->fetch_assoc();
            if ($indirizzo) {
                foreach ($indirizzo as $key => $value) {
                    $show->setContent($key, $value);
                }
            }
        } while ($indirizzo);
        $controllo = "modifica indirizzo";
        $show->setContent("controllo", $controllo);
        $body->setContent("content", $show->get());
        $main->setContent("content", $body->get());
        $main->close();
    } else {
        $main->setContent("title", "Crea indirizzo");
        $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/addresses/form_indirizzo.html");
        $email = $_SESSION['user']['email'];
        $oid = $mysqli->query("SELECT id as id_utente FROM users WHERE email = '$email'");
        $utente = $oid->fetch_assoc();
        $show->setContent("id_utente", $utente['id_utente']);
        $controllo = "nuovo indirizzo";
        $show->setContent("controllo", $controllo);
        $body->setContent("content", $show->get());
        $main->setContent("content", $body->get());
        $main->close();
    }
}

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
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $indirizzo = $_POST['indirizzo'];
    $citta = $_POST['citta'];
    $cap = $_POST['cap'];
    $provincia = $_POST['provincia'];
    $nazione = $_POST['nazione'];
    $email = $_SESSION['user']['email'];
    $oid = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
    $utente = $oid->fetch_assoc();
    $mysqli->query("INSERT INTO tdw_ecommerce.indirizzi SET indirizzo = '$indirizzo', citta = '$citta', cap = '$cap', provincia = '$provincia', nazione = '$nazione', users_id = '$utente[id]'");
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Indirizzo aggiunto con successo";
    } elseif ($mysqli->affected_rows == 0) {
        $response['warning'] = "Nessun dato aggiunto";
    } else {
        $response['error'] = "Errore nell'aggiunta dell'indirizzo";
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