<?php

use JetBrains\PhpStorm\NoReturn;

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
require $_SERVER['DOCUMENT_ROOT'] . "/include/auth.inc.php";
require "include/dbms.inc.php";
global $mysqli;

checkSession();

/**
 * Metodo per la gestione della pagina di accesso.
 * @return void
 */
function login(): void
{
    if (!(isset($_SESSION['auth']) && $_SESSION['auth'] = true)) {  //se non è autenticato
        if ($_SERVER["REQUEST_METHOD"] == "POST") {                 //se è una richiesta POST
            doLogin();                                              //eseguo il login
            if (isset($_SESSION['auth']) && $_SESSION['auth'] = true) {     //se l'utente è autenticato dopo la funzione di login
                redirect($_POST['referrer']??"");
            } else {                                                 //se l'utente non è stato autenticato
                $main = setupMainAuth("login");
                $alert = setupAlert("Username o password errati.");
                $main->setContent("alert", $alert->get());
                $main->close();
            }
        } else {                                                      //se è una richiesta GET
            $main = setupMainAuth("login");
            $main->close();
        }
        //se l'utente è già autenticato, reindirizza alla home
    } else {
        redirect($_POST['referrer']??"");
    }
}

/**
 * Registrazione di un utente.
 * @return void
 */
function register(): void
{
    if (!(isset($_SESSION['auth']) && $_SESSION['auth'] = true)) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            doRegister();
            if (isset($_SESSION['auth']) && $_SESSION['auth'] = true) {
                redirect($_POST['referrer']??"");
            } else {
                $main = setupMainAuth("register");
                $alert = setupAlert("Email gi&agrave in uso.");
                $main->setContent("alert", $alert->get());
                $main->close();
            }
        } else {
            $main = setupMainAuth("register");
            $main->close();
        }
    } else {
        redirect($_POST['referrer']??"");
    }
}

/**
 * Logout di un utente.
 * @return void
 */
#[NoReturn] function logout(): void
{
    if ($_SESSION['auth'] = true) {
        unset($_SESSION['auth']);   //rimozione dell'autenticazione
        unset($_SESSION['user']);   //rimozione dell'utente
    }
    header("Location: /");
    exit;
}


/**
 * Utility per la redirezione alla home dopo login o registrazione
 * @return void
 */
#[NoReturn] function redirect($referrer): void
{
    //se è stato impostato un referrer reindirizza
    echo "Referrer".$referrer;
    if ($referrer != "") {
        unset($_SESSION['referrer']);
        header("Location: $referrer");
        exit;
    } else if (isset($_SESSION['user']['script']['/admin']) && $_SESSION['user']['script']['/admin']) {
        Header("Location: /admin");               //se è un admin vai alla pagina di amministrazione
        exit;
    } else {
        Header("Location: /");                    //se non è admin vai alla home
        exit;
    }
}


