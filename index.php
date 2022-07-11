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
global $mysqli;

$query = "SELECT * FROM services where '" . $request . "' like url ORDER BY LENGTH(url) DESC LIMIT 1;";
$oid = $mysqli->query($query);

if ($oid->num_rows > 0) {
    $oid = $oid->fetch_assoc();
    if (!(str_starts_with($oid['script'], "user") || str_starts_with($oid['script'], "admin"))) {       //se si accede a una pagina pubblica
        $controller = $oid['script'];                           //carico il controller
        $action = $oid['callback'];                             //carico la funzione da eseguire
        $controller = __CONTROLLERS__ . $controller;            //carico il file del controller
        require $controller;
        $action();                                              //eseguo la funzione
    } else if (isset($_SESSION['auth']) && $_SESSION['auth']) { //se è autenticato
        if (isset($_SESSION['user']['script'][$oid['url']])) {  //se è autorizzato
            $controller = $oid['script'];                           //carico il controller
            $action = $oid['callback'];                             //carico la funzione da eseguire
            $controller = __CONTROLLERS__ . $controller;            //carico il file del controller
            require $controller;
            $action();                                              //eseguo la funzione
        } else {    //se è autenticato ma non ha accesso alla pagina, reindirizzo alla home
            Header("Location: /");
            exit;
        }
    } else {        //se non è autenticato reindirizza alla login
        Header("Location: /login");
        exit;
    }
}else{  //se la pagina non esiste, carico il controller degli errori
    require __CONTROLLERS__ . 'errors.php';
}
