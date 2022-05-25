<?php
/**
 * Routing page
 */
$request = $_SERVER['REQUEST_URI'];
const __CONTROLLERS__ = __DIR__.'/controllers/';

switch ($request) {
    case '':
    case '/' :
        require __CONTROLLERS__.'home.php';
        break;
    case '/about' :
        require  __CONTROLLERS__.'about.php';
        break;
    case '/contacts' :
        require  __CONTROLLERS__.'contacts.php';
        break;
    case '/products' :
        require  __CONTROLLERS__.'products/index.php';
        break;
    case '/product/{*}' :
        require  __CONTROLLERS__.'products/show.php';
        break;
    case '/cart' :
        require  __CONTROLLERS__.'cart.php';
        break;
    default:
        require  __CONTROLLERS__.'errors.php';
        break;
}