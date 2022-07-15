<?php

use JetBrains\PhpStorm\NoReturn;

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

#[NoReturn] function like(): void
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $user = $_SESSION['user']['id'];
    $response = array();
    if ($_POST['like'] === "0") {
        $mysqli->query("DELETE FROM tdw_ecommerce.users_has_prodotti_preferiti WHERE users_id = $user AND prodotti_id = $id");
        $response['success'] = 'Dislike';
    } else {
        $mysqli->query("INSERT INTO tdw_ecommerce.users_has_prodotti_preferiti (users_id, prodotti_id) VALUES ($user, $id)");
        $response['success'] = 'Like';
    }
    exit(json_encode($response));
}