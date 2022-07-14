<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function about(): void
{
    global $mysqli;
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/about.html");

    $personalizzazione = $mysqli->query("SELECT titolo_descrizione, descrizione_estesa, immagine_about FROM personalizzazione WHERE id = 1")->fetch_assoc();
    if ($personalizzazione) {
        $body->setContent("titolo_descrizione", $personalizzazione["titolo_descrizione"]);
        $body->setContent("descrizione_estesa", $personalizzazione["descrizione_estesa"]);
        if ($personalizzazione["immagine_about"] != "") {
            $body->setContent("immagine_about", "/uploads/".$personalizzazione["immagine_about"]);
        } else {
            $body->setContent("immagine_about", "https://via.placeholder.com/500");
        }
    }

    $main->setContent("title", "ABOUT US");
    $main->setContent("content", $body->get());
    $main->close();
}
