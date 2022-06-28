<?php

function setupMain()
{
    $main = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/main.html");
// Default set delle parti statiche
    $main->setContent("header", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/header.html"))->get());
    $main->setContent("sidebar", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/sidebar.html"))->get());
    $main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/footer.html"))->get());
    return $main;
}
