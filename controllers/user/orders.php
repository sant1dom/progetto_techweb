<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function show(): void
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $ordine = $mysqli->query("SELECT ordini.id, u.email as email_utente, u.id as id_utente, u.telefono as utente_telefono,
                                            ordini.data, ordini.stato, ordini.totale, ordini.numero_ordine, ordini.motivazione, 
                                            CONCAT(isp.indirizzo,' ', isp.citta,' ', isp.cap,' ', isp.provincia,' ',isp.nazione) as indirizzo_spedizione,
                                            CONCAT(ifa.indirizzo,' ', ifa.citta,' ', ifa.cap,' ', ifa.provincia,' ', ifa.nazione) as indirizzo_fatturazione 
                                    FROM tdw_ecommerce.ordini 
                                        JOIN tdw_ecommerce.users as u on u.id = ordini.user_id 
                                        JOIN tdw_ecommerce.indirizzi as isp on isp.id = ordini.indirizzi_spedizione 
                                        JOIN tdw_ecommerce.indirizzi as ifa on ifa.id = ordini.indirizzi_fatturazione
                                        WHERE ordini.id = '$id'");
    $ordine = $ordine->fetch_assoc();
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/orders/show.html");

    $ordine['data'] = date("d/m/Y", strtotime($ordine['data']));
    foreach ($ordine as $key => $value) {
        $body->setContent($key, $value);
    }

    $table_prodotti = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/orders/prodotti.html");
    $oid = $mysqli->query("SELECT p.nome as nome_prodotto,p.id as id, p.prezzo as prezzo_prodotto, op.quantita as quantita_prodotto, percentuale as sconto
                                FROM tdw_ecommerce.ordini_has_prodotti as op 
                                    JOIN tdw_ecommerce.prodotti as p on p.id=op.prodotti_id
                                WHERE op.ordini_id=" . $id);
    do {
        $prodotti = $oid->fetch_assoc();
        if ($prodotti) {
            $image = $mysqli->query("SELECT nome_file as image FROM immagini JOIN prodotti p on immagini.prodotto_id = '{$prodotti['id']}' ORDER BY immagini.id LIMIT 1 ");
            $image = $image->fetch_assoc();
            if (!$image) {
                $table_prodotti->setContent('image', 'https://via.placeholder.com/150');
            } else {
                $table_prodotti->setContent('image', "/uploads/" . $image["image"]);
            }

            if($prodotti['sconto'] == 0) {
                $prodotti['sconto'] = '-';
            } else {
                $prodotti['sconto'] = $prodotti['sconto'] . '%';
            }

            foreach ($prodotti as $key => $value) {
                $table_prodotti->setContent($key, $value);
            }
        }
    } while ($prodotti);

    $selectPicker_indirizzi = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/orders/selectIndirizzi.html");
    $oid = $mysqli->query("SELECT id, CONCAT(indirizzo,' ', citta,' ', cap,' ', provincia,' ', nazione) as indirizzo FROM tdw_ecommerce.indirizzi WHERE users_id=" . $ordine['id_utente']);
    do {
        $indirizzi = $oid->fetch_assoc();
        if ($indirizzi) {
            foreach ($indirizzi as $key => $value) {
                $selectPicker_indirizzi->setContent($key, $value);
            }
        }
    } while ($indirizzi);
    $form_indirizzo = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/orders/formIndirizzi.html");
    $form_indirizzo->setContent("id_ordine", $ordine['id']);
    $selectPicker_indirizzi->setContent("id_ordine", $ordine['id']);
    $body->setContent("selectPicker_indirizzi", $selectPicker_indirizzi->get());
    $body->setContent("form_indirizzo", $form_indirizzo->get());
    $body->setContent("table_prodotti", $table_prodotti->get());
    $main->setContent("title", "Ordine n??" . $ordine['numero_ordine']);
    $main->setContent("content", $body->get());
    $main->close();
}

function annulla(): void
{
    global $mysqli;
    $id = $_POST['id'];
    $mysqli->query("UPDATE tdw_ecommerce.ordini SET stato='ANNULLATO', motivazione='Annullamento da parte del utente' WHERE id=" . $id);
    $oid = $mysqli->query("SELECT p.id as id_prodotto, op.quantita as quantita_prodotto FROM tdw_ecommerce.ordini_has_prodotti as op JOIN tdw_ecommerce.prodotti as p on p.id=op.prodotti_id WHERE op.ordini_id=" . $id);
    $prodotti = $oid->fetch_assoc();
    if ($prodotti) {
        do {
            $mysqli->query("UPDATE tdw_ecommerce.prodotti SET quantita_disponibile = quantita_disponibile+" . $prodotti['quantita_prodotto'] . " WHERE id=" . $prodotti['id_prodotto']);
        } while ($prodotti = $oid->fetch_assoc());
    }
}

function edit(): void
{
    global $mysqli;
    $controllo = $_POST['controllo'];
    $id_ordine = $_POST['id_ordine'];
    if ($controllo === "indirizzo_esistente") {
        $id_indirizzo = $_POST['id_indirizzo'];
        $response = array();
        $mysqli->query("UPDATE tdw_ecommerce.ordini SET motivazione='', stato='IN ATTESA',indirizzi_spedizione=" . $id_indirizzo . ",indirizzi_fatturazione=" . $id_indirizzo . " WHERE id=" . $id_ordine);


    } else if ($controllo == "indirizzo_nuovo") {
        $indirizzo = $_POST['indirizzo'];
        $citta = $_POST['citta'];
        $cap = $_POST['cap'];
        $provincia = $_POST['provincia'];
        $nazione = $_POST['nazione'];
        $email = $_SESSION['user']['email'];
        $oid = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
        $utente = $oid->fetch_assoc();
        $mysqli->query("INSERT INTO tdw_ecommerce.indirizzi (indirizzo, citta, cap, provincia, nazione, users_id) VALUES ('$indirizzo', '$citta', '$cap', '$provincia', '$nazione', " . $utente['id'] . ")");
        $id_indirizzo = $mysqli->insert_id;
        $mysqli->query("UPDATE tdw_ecommerce.ordini SET motivazione='', stato='IN ATTESA', indirizzi_spedizione=" . $id_indirizzo . ",indirizzi_fatturazione=" . $id_indirizzo . " WHERE id=" . $id_ordine);
    }
}

function create(): void
{
    global $mysqli;
    $user = $mysqli->query("SELECT id FROM users WHERE email = '{$_SESSION['user']['email']}'")->fetch_assoc();
    $order_id = 0;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['address']) && isset($_POST['pagamento'])) {
            $id_indirizzo = $_POST['address'];
            if ($id_indirizzo == 0) {
                $nazione = $_POST['nazione'];
                $citta = $_POST['citta'];
                $provincia = $_POST['provincia'];
                $cap = $_POST['cap'];
                $indirizzo = $_POST['indirizzo'];

                $mysqli->query("INSERT INTO tdw_ecommerce.indirizzi (indirizzo, citta, cap, provincia, nazione, users_id) VALUES ('$indirizzo', '$citta', '$cap', '$provincia', '$nazione', " . $user['id'] . ")");
                $id_indirizzo = $mysqli->insert_id;
            }

            $oid = $mysqli->query("SELECT quantity, prezzo, percentuale as sconto 
                                            FROM tdw_ecommerce.cart 
                                                JOIN prodotti p on p.id = cart.products_id 
                                                LEFT JOIN offerte o on p.id = o.prodotti_id  
                                            WHERE users_id = " . $user['id']);
            $total = 0.00;
            do {
                $product = $oid->fetch_assoc();
                if ($product) {
                    if (isset($product['sconto'])) {
                        $total += $product['quantity'] * $product['prezzo'] * (1 - $product['sconto'] / 100);
                    } else {
                        $total += $product['quantity'] * $product['prezzo'];
                    }
                }
            } while ($product);

            $rnd_str = generateRandomString();
            $mysqli->query("INSERT INTO tdw_ecommerce.ordini (user_id, `data`, stato, totale, numero_ordine, indirizzi_fatturazione, indirizzi_spedizione, metodi_pagamento, motivazione) 
                                    VALUES ({$user['id']}, NOW(), 'NUOVO', $total, $rnd_str, $id_indirizzo, $id_indirizzo, '{$_POST['pagamento']}', '')");
            if ($mysqli->affected_rows > 0) {
                $order_id = $mysqli->insert_id;
                $oid = $mysqli->query("SELECT products_id, quantity, percentuale as sconto FROM tdw_ecommerce.cart  LEFT JOIN offerte o on products_id = o.prodotti_id  WHERE users_id = " . $user['id']);
                do {
                    $product = $oid->fetch_assoc();
                    if ($product) {
                        if ($product['sconto'] === null) {
                            $product['sconto'] = 0;
                        }
                        $mysqli->query("INSERT INTO tdw_ecommerce.ordini_has_prodotti (ordini_id, prodotti_id, quantita, percentuale) VALUES ('$order_id', {$product['products_id']}, {$product['quantity']}, {$product['sconto']})");
                        if ($mysqli->affected_rows > 0) {
                            $mysqli->query("UPDATE tdw_ecommerce.prodotti SET quantita_disponibile=quantita_disponibile-{$product['quantity']} WHERE id={$product['products_id']}");
                            $mysqli->query("DELETE FROM tdw_ecommerce.cart WHERE users_id = {$user['id']} AND products_id = {$product['products_id']}");
                        }
                    }
                } while ($product);
            } else {
                Header("Location: /cart");
            }
        } else {
            Header("Location: /cart");
        }
    } else {
        Header("Location: /cart");
    }
    if ($order_id > 0) {
        Header("Location: /orders/{$order_id}");
    } else {
        Header("Location: /cart");
    }
}