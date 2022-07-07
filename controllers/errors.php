<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
//require "include/dbms.inc.php";
//global $mysqli;

if(!str_starts_with($_SERVER['REQUEST_URI'], "/admin/")){
    //TODO:parametrizzare i messaggi di errore
    $error = "404";
    $title = "Pagina non trovata";
    $description = "La pagina che stai cercando non esiste";

    $main = setupMainWizym();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/error.html");
    $data = array(
        "error" => $error,
        "title" => $title,
        "description" => $description,
    );

    foreach ($data as $key => $value) {
        $body->setContent($key, $value);
    }

    $main->setContent("content", $body->get());
    $main->close();
} else {
    $main = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/404.html");
    $main->close();
}
