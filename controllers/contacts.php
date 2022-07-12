<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function contacts (): void
{
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/contacts.html");

    $main->setContent("title", "CONTACT US");
    $main->setContent("content",$body->get());
    $main->close();
}

