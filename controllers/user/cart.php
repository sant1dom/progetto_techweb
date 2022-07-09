<?php

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function cart(): void
{
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "I miei ordini");
    $body = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/cart.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/components/cart_table.html");

    $user = $mysqli->query("SELECT * FROM tdw_ecommerce.users WHERE email = '{$_SESSION["user"]["email"]}'");
    $user = $user->fetch_assoc();
    $products = $mysqli->query("SELECT id, nome, prezzo, dimensione, quantity FROM tdw_ecommerce.prodotti JOIN cart c on prodotti.id = c.products_id WHERE c.users_id = {$user["id"]}");

    if ($mysqli->affected_rows != 0) {
        $colnames = array(
            "Immagine",
            "Nome del prodotto",
            "Prezzo",
            "Quantità",
            "Dimensione",
            "Totale",
            "Rimuovi"
        );

        foreach ($colnames as $colname) {
            $table->setContent('colname', $colname);
        }
        $specific_table = new Template($_SERVER['DOCUMENT_ROOT']."/skins/wizym/dtml/components/specific_tables/prodotti_carrello.html");
        do {
            $product = $products->fetch_assoc();
            if ($product) {
                foreach ($products as $value) {
                    foreach ($value as $k => $v) {
                        $specific_table->setContent($k, $v);
                    }
                }
            }
        } while ($product);
        $table->setContent("specific_table", $specific_table->get());
    } else {
        $table->setContent('colname', "Non ci sono prodotti nel carrello");
    }


    $body->setContent("cart_table", $table->get());
    $main->setContent("title", "CART");
    $main->setContent("content",$body->get());
    $main->close();

}
