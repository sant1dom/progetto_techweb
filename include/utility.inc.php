<?php

function setupMainAdmin()
{
    $main = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/main.html");
// Default set delle parti statiche
    $main->setContent("header", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/header.html"))->get());
    $main->setContent("sidebar", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/sidebar.html"))->get());
    $main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/footer.html"))->get());
    return $main;
}

function setupMainUser()
{
    checkSession();
    $main = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/main.html");
    // Default set delle parti statiche
    $main->setContent("header", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/header.html"))->get());
    $main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/footer.html"))->get());
    return $main;
}

/**
 * Imposta il main per l'autenticazione.
 *
 * @param string $page
 * @return Template
 */
function setupMainAuth(string $page): Template
{
    checkSession();

    $main = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/auth/" . $page . ".html");
    // Default set delle parti statiche per il template wizym
    $main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/footer.html"))->get());
    return $main;
}

function setupAlert(string $msg): Template
{
    $alert = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/alert.html");
    $alert->setContent("message", $msg); //imposto il messaggio di errore per l'alert
    return $alert;
}

/**
 * Controlla se la sessione esiste, in caso contrario la crea.
 * DA CHIAMARE IN OGNI CONTROLLER.
 * Se si usano i metodi di setup del main non è necessario richiamare questo metodo.
 *
 * @return void
 */
function checkSession(): void
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Utility che stampa sulla console del browser l'elemento passato.
 *
 * @param $data
 * @return void
 */
function debug_to_console($data): void
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}