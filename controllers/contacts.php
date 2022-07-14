<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function contacts(): void
{
    global $mysqli;
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/contacts.html");

    $personalizzazione = $mysqli->query("SELECT phone, email, address, country FROM personalizzazione WHERE id = 1")->fetch_assoc();

    if ($personalizzazione) {
        foreach ($personalizzazione as $key => $value) {
            $body->setContent($key, $value);
        }
    }


    $main->setContent("title", "Contattaci");
    $main->setContent("content", $body->get());
    $main->close();
}

