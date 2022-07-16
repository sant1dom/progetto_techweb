<?php

function setupMainAdmin()
{
    global $mysqli;
    $main = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/main.html");
    // Default set delle parti statiche
    $header = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/header.html");
    $personalizzazione = $mysqli->query("SELECT logo FROM personalizzazione WHERE id = 1")->fetch_assoc();
    $header->setContent("logo", "/uploads/".$personalizzazione["logo"] ?? "https://via.placeholder.com/150");
    $header->setContent("nome_utente", $_SESSION["user"]["nome"]." ".$_SESSION["user"]["cognome"]);
    $main->setContent("header", $header->get());

    $sidebar = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/sidebar.html");
    $sidebar->setContent("logo", "/uploads/".$personalizzazione["logo"] ?? "https://via.placeholder.com/150");
    $main->setContent("sidebar", $sidebar->get());
    $main->setContent("footer", (new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/footer.html"))->get());
    return $main;
}

function setupMainUser()
{
    checkSession();
    global $mysqli;

    $main = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/main.html");
    // Default set delle parti statiche
    if (isset($_SESSION['user'])) {
        $logged = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/user/logged.html");
        if (isset($_SESSION['user']['script']['/admin'])) {
            $logged->setContent("admin", "<li><a href='/admin'>Amministrazione</a></li>");
        }
        $main->setContent("user_bar", $logged->get());
    } else {
        $unlogged = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/user/unlogged.html");
        $unlogged->setContent("referrer", "?referrer=".urlencode($_SERVER['REQUEST_URI']));
        $main->setContent("user_bar", $unlogged->get());
    }

    $header = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/header.html");
    $footer = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/footer.html");
    $personalizzazione = $mysqli->query("SELECT logo FROM personalizzazione WHERE id = 1")->fetch_assoc();
    if ($personalizzazione) {
        if ($personalizzazione["logo"] != "") {
            $header->setContent("logo", "/uploads/".$personalizzazione["logo"]);
            $footer->setContent("logo", "/uploads/".$personalizzazione["logo"]);
        } else {
            $header->setContent("logo", "https://via.placeholder.com/150");
            $footer->setContent("logo", "https://via.placeholder.com/150");
        }
    }

    $main->setContent("header", $header->get());
    $main->setContent("footer", $footer->get());
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

    global $mysqli;
    $main = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/auth/" . $page . ".html");
    $footer = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/footer.html");
    $personalizzazione = $mysqli->query("SELECT logo FROM personalizzazione WHERE id = 1")->fetch_assoc();
    if ($personalizzazione) {
        if (!$personalizzazione["logo"] == "") {
            $personalizzazione["logo"] = "/uploads/" . $personalizzazione["logo"];
        } else {
            $personalizzazione["logo"] = "https://via.placeholder.com/500";
        }
        $footer->setContent("logo", $personalizzazione["logo"]);
        $main->setContent("logo", $personalizzazione["logo"]);
    }


    $main->setContent("referrer", $_GET['referrer'] ?? "");
    // Default set delle parti statiche per il template wizym
    $main->setContent("footer", $footer->get());
    return $main;
}

function setupAlert(string $msg): Template
{
    $alert = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/alert.html");
    $alert->setContent("message", $msg); //imposto il messaggio di errore per l'alert
    return $alert;
}

function generateRandomString($length = 10) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
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
    if(is_array($output)){
        $output = implode(',', $output);
    }

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}