<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/include/dbms.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/utility.inc.php";
/**
 * Routing page
 */
checkSession();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$request = strtok($_SERVER["REQUEST_URI"], '?');
const __CONTROLLERS__ = __DIR__ . '/controllers/';
const __PUBLIC_DIRS__ = ["/", "/about", "/contacts", "/login", "/logout", "/register"];
global $mysqli;

$query = "SELECT * FROM services where '" . $request . "' like url ORDER BY LENGTH(url) DESC LIMIT 1;";
$oid = $mysqli->query($query);

if ($oid->num_rows > 0) {
    $oid = $oid->fetch_assoc();

    if ((isset($_SESSION['auth']) && $_SESSION['auth'] &&           //se l'utente è autenticato
            isset($_SESSION['user']['script'][$oid['url']]) &&      //e ha accesso alla pagina
            $_SESSION['user']['script'][$oid['url']]) ||
        in_array($oid['url'], __PUBLIC_DIRS__)) {       //o è in una delle pagine pubbliche
        $controller = $oid['script'];                           //carico il controller
        $action = $oid['callback'];                             //carico la funzione da eseguire
        $controller = __CONTROLLERS__ . $controller;            //carico il file del controller
        require $controller;
        $action();                                              //eseguo la funzione
    } else {
        Header("Location: /");                           //se non ha accesso alla pagina, reindirizzo alla home
        exit;
    }
}else{
    require __CONTROLLERS__ . 'errors.php';                     //se la pagina non esiste, carico il controller degli errori
}
