<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";
function index()
{
    global $mysqli;
    $main = setupMainUser();
    $main->setContent("title", "I miei ordini");
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
    $colnames = array(
        "Numero ordine",
        "Data",
        "Stato",
        "Totale",
        "Indirizzo di spedizione",
        "Indirizzo di fatturazione"
    );
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/table.html");
    foreach ($colnames as $colname) {
        $table->setContent("colname", $colname);
    }
    $specific_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/ordini_utente.html");
    $email = $_SESSION['user']['email'];
    $oid = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
    $utente = $oid->fetch_assoc();
    $oid = $mysqli->query("SELECT ordini.id, u.email as utente, ordini.data, ordini.stato, ordini.totale, ordini.numero_ordine, CONCAT(isp.indirizzo,' ', isp.citta,' ', isp.cap,' ', isp.provincia,' ',isp.nazione) as indirizzo_spedizione,
    CONCAT(ifa.indirizzo,' ', ifa.citta,' ', ifa.cap,' ', ifa.provincia,' ', ifa.nazione) as indirizzo_fatturazione FROM tdw_ecommerce.ordini JOIN tdw_ecommerce.users as u on u.id=ordini.user_id JOIN tdw_ecommerce.indirizzi as isp on isp.id= ordini.indirizzi_spedizione JOIN tdw_ecommerce.indirizzi as ifa on ifa.id= ordini.indirizzi_fatturazione WHERE ordini.user_id = '$utente[id]'");
    do {
        $ordini = $oid->fetch_assoc();
        if ($ordini) {
            foreach ($ordini as $key => $value) {
                $specific_table->setContent($key, $value);
            }
        }
    } while ($ordini);
    $table->setContent("specific_table", $specific_table->get());
    $body->setContent("content", $table->get());
    $main->setContent("content", $body->get());
    $main->close();
}

function show()
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[2];
    $ordine = $mysqli->query("SELECT ordini.id, u.email as email_utente, u.nome as nome_utente, u.id as id_utente, u.cognome as cognome_utente, u.telefono as utente_telefono, ordini.metodi_pagamento as m,
       ordini.data, ordini.stato, ordini.totale, ordini.numero_ordine, ordini.motivazione, CONCAT(isp.indirizzo,' ', isp.citta,' ', isp.cap,' ', isp.provincia,' ',isp.nazione) as indirizzo_spedizione,
       CONCAT(ifa.indirizzo,' ', ifa.citta,' ', ifa.cap,' ', ifa.provincia,' ', ifa.nazione) as indirizzo_fatturazione FROM tdw_ecommerce.ordini 
            JOIN tdw_ecommerce.users as u on u.id=ordini.user_id JOIN tdw_ecommerce.indirizzi as isp on isp.id= ordini.indirizzi_spedizione JOIN tdw_ecommerce.indirizzi as ifa on ifa.id= ordini.indirizzi_fatturazione");
    $ordine = $ordine->fetch_assoc();
    $main = setupMainUser();
    $body = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/user/my_account.html");
    $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/orders/show_ordine.html");
    foreach ($ordine as $key => $value) {
        $show->setContent($key, $value);
    }
    $table_prodotti = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/prodotti_ordine.html");
    $oid = $mysqli->query("SELECT p.nome as nome_prodotto,p.id as id, p.prezzo as prezzo_prodotto, op.quantita as quantita_prodotto FROM tdw_ecommerce.ordini_has_prodotti as op JOIN tdw_ecommerce.prodotti as p on p.id=op.prodotti_id WHERE op.ordini_id=" . $id);
    do {
        $prodotti = $oid->fetch_assoc();
        if ($prodotti) {
            foreach ($prodotti as $key => $value) {
                $table_prodotti->setContent($key, $value);
            }
        }
    } while ($prodotti);
    $selectPicker_indirizzi = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/specific_tables/indirizzi_modifica_ordine.html");
    $oid = $mysqli->query("SELECT id, CONCAT(indirizzo,' ', citta,' ', cap,' ', provincia,' ', nazione) as indirizzo FROM tdw_ecommerce.indirizzi WHERE users_id=" . $ordine['id_utente']);
    do {
        $indirizzi = $oid->fetch_assoc();
        if ($indirizzi) {
            foreach ($indirizzi as $key => $value) {
                $selectPicker_indirizzi->setContent($key, $value);
            }
        }
    } while ($indirizzi);
    $form_indirizzo = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/wizym/dtml/components/addresses/form_indirizzo.html");
    $form_indirizzo->setContent("id_ordine", $ordine['id']);
    $selectPicker_indirizzi->setContent("id_ordine", $ordine['id']);
    $show->setContent("selectPicker_indirizzi", $selectPicker_indirizzi->get());
    $show->setContent("form_indirizzo", $form_indirizzo->get());
    $show->setContent("table_prodotti", $table_prodotti->get());
    $body->setContent("content", $show->get());
    $main->setContent("title", "Ordine n°" . $ordine['numero_ordine']);
    $main->setContent("content", $body->get());
    $main->close();
}

function annulla()
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

function edit()
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

function create()
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

            $oid = $mysqli->query("SELECT quantity, prezzo FROM tdw_ecommerce.cart JOIN prodotti p on p.id = cart.products_id WHERE users_id = " . $user['id']);
            $total = 0.00;
            do {
                $product = $oid->fetch_assoc();
                if ($product) {
                    $total = $total + $product['quantity'] * $product['prezzo'];
                }
            } while ($product);

            $rnd_str = generateRandomString();
            $mysqli->query("INSERT INTO tdw_ecommerce.ordini (user_id, `data`, stato, totale, numero_ordine, indirizzi_fatturazione, indirizzi_spedizione, metodi_pagamento, motivazione) 
                                    VALUES ({$user['id']}, NOW(), 'NUOVO', $total, $rnd_str, $id_indirizzo, $id_indirizzo, '{$_POST['pagamento']}', '')");
            if ($mysqli->affected_rows > 0) {
                $order_id = $mysqli->insert_id;
                $oid = $mysqli->query("SELECT products_id, quantity FROM tdw_ecommerce.cart WHERE users_id = " . $user['id']);
                do {
                    $product = $oid->fetch_assoc();
                    if ($product) {
                        $mysqli->query("INSERT INTO tdw_ecommerce.ordini_has_prodotti (ordini_id, prodotti_id, quantita) VALUES ($order_id, {$product['products_id']}, {$product['quantity']})");
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