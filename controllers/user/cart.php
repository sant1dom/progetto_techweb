<?php

require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function cart(): void
{
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "Il mio carrello");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/cart.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/cart_table.html");

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
        $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/prodotti_carrello.html");
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

    $oid = $mysqli->query("SELECT * FROM indirizzi WHERE users_id = {$user["id"]}");

    do {
        $address = $oid->fetch_assoc();
        if ($address) {
            foreach ($address as $key => $value) {
                if ($key == "id") {
                    $body->setContent("address_id", $value);
                } else {
                    $body->setContent($key, $value);
                }
            }
        }
    } while ($address);

    $body->setContent("cart_table", $table->get());
    $main->setContent("title", "CART");
    $main->setContent("content", $body->get());
    $main->close();
}

function edit()
{
    $response = array();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['id']) && isset($_POST['quantity'])) {
            global $mysqli;
            $id = $_POST['id'];
            $quantity = $_POST['quantity'];

            if ($quantity <= 0) {
                $response['error'] = "La quantità deve essere maggiore di 0";
                exit(json_encode($response));
            }


            $oid = $mysqli->query("SELECT quantita_disponibile FROM prodotti WHERE id = {$id}");
            $quantita_disponibile = $oid->fetch_assoc();
            if ($quantita_disponibile['quantita_disponibile'] >= $quantity) {
                $user = $mysqli->query("SELECT * FROM tdw_ecommerce.users WHERE email = '{$_SESSION["user"]["email"]}'");
                $user = $user->fetch_assoc();
                $mysqli->query("UPDATE tdw_ecommerce.cart SET quantity = $quantity WHERE users_id = {$user["id"]} AND products_id = $id");
                if ($mysqli->affected_rows == 1) {
                    $response['success'] = "Successo";
                } else {
                    $response['error'] = "Errore nell'aggiornamento.";
                }
            } else {
                $response['error'] = "Quantità non disponibile.";
            }
        }
    }
    exit(json_encode($response));
}

function remove()
{
    $response = array();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['id'])) {
            global $mysqli;
            $id = $_POST['id'];
            if ($id <= 0) {
                $response['error'] = "Errore nell'aggiornamento: (quantità inferiore o uguale a 0).";
                exit(json_encode($response));
            }
            $user = $mysqli->query("SELECT * FROM tdw_ecommerce.users WHERE email = '{$_SESSION["user"]["email"]}'");
            $user = $user->fetch_assoc();
            $mysqli->query("DELETE FROM tdw_ecommerce.cart WHERE users_id = {$user["id"]} AND products_id = $id");
            if ($mysqli->affected_rows == 1) {
                $response['success'] = "Successo";
            } else {
                $response['error'] = "Errore nell'aggiornamento.";
            }
        }
    }
    exit(json_encode($response));
}
