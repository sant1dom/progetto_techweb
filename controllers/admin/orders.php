<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/template.inc.php";

function index()
{
    global $mysqli;
    $colnames = array(
        "Numero ordine",
        "Utente",
        "Data",
        "Stato",
        "Totale",
       //"Indirizzo di spedizione",
        //"Indirizzo di fatturazione"
    );
    $oid = $mysqli->query("SELECT ordini.id, u.email as utente, ordini.data, ordini.stato, ordini.totale, ordini.numero_ordine, CONCAT(isp.indirizzo,' ', isp.citta,' ', isp.cap,' ', isp.provincia,' ',isp.nazione) as indirizzo_spedizione,
        CONCAT(ifa.indirizzo,' ', ifa.citta,' ', ifa.cap,' ', ifa.provincia,' ', ifa.nazione) as indirizzo_fatturazione FROM tdw_ecommerce.ordini JOIN tdw_ecommerce.users as u on u.id=ordini.user_id JOIN tdw_ecommerce.indirizzi as isp on isp.id= ordini.indirizzi_spedizione JOIN tdw_ecommerce.indirizzi as ifa on ifa.id= ordini.indirizzi_fatturazione");
    $main = setupMainAdmin();
    // Creazione del contenuto
    $crud = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/views/crud.html");
    $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/table.html");
    // Riempimento della tabella
    $table->setContent("title", "Ordini");
    foreach ($colnames as $value) {
        $table->setContent("colname", $value);
    }
    $ordini_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/ordini.html");
    do {
        $ordini = $oid->fetch_assoc();
        if ($ordini) {
            foreach ($ordini as $key => $value) {
                $ordini_table->setContent($key, $value);
            }
        }
    } while ($ordini);
    $table->setContent("sptable", $ordini_table->get());
    $crud->setContent("table", $table->get());
    $main->setContent("content", $crud->get());
    $main->close();
}

function show()
{
    global $mysqli;
    $colnames = array(
        "Nome",
        "Prezzo",
        "Quantità",
    );
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $ordine= $mysqli-> query("SELECT ordini.id, u.email as email_utente, u.nome as nome_utente, u.cognome as cognome_utente, u.telefono as utente_telefono, m.numero_carta as carta_utente, m.nome_proprietario as nome_carta,
       m.scadenza_carta as scadenza_carta, m.cvv as cvv, ordini.data, ordini.stato, ordini.totale, ordini.numero_ordine, ordini.motivazione, CONCAT(isp.indirizzo,' ', isp.citta,' ', isp.cap,' ', isp.provincia,' ',isp.nazione) as indirizzo_spedizione,
        CONCAT(ifa.indirizzo,' ', ifa.citta,' ', ifa.cap,' ', ifa.provincia,' ', ifa.nazione) as indirizzo_fatturazione FROM tdw_ecommerce.ordini 
            JOIN tdw_ecommerce.users as u on u.id=ordini.user_id JOIN tdw_ecommerce.indirizzi as isp on isp.id= ordini.indirizzi_spedizione JOIN tdw_ecommerce.indirizzi as ifa on ifa.id= ordini.indirizzi_fatturazione
            JOIN tdw_ecommerce.metodi_pagamento as m on m.id=ordini.metodi_pagamento WHERE ordini.id=".$id);

    if ($ordine->num_rows == 0) {
        header("Location: /admin/orders");
    } else {
        $ordine = $ordine->fetch_assoc();
        $main = setupMainAdmin();
        $show = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/orders/show.html");
        foreach ($ordine as $key => $value) {
            $show->setContent($key, $value);
        }
        $table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/simple_table.html");
        $table->setContent("title", "Prodotti");
        foreach ($colnames as $value) {
            $table->setContent("colname", $value);
        }
        $prodotti_table = new Template($_SERVER['DOCUMENT_ROOT'] . "/skins/admin/sash/dtml/components/specific_tables/prodotti_ordini.html");
        $oid= $mysqli-> query("SELECT p.nome as nome_prodotto, p.prezzo as prezzo_prodotto, op.quantita as quantita_prodotto FROM tdw_ecommerce.ordini_has_prodotti as op JOIN tdw_ecommerce.prodotti as p on p.id=op.prodotti_id WHERE op.ordini_id=".$id);
        do {
            $prodotti = $oid->fetch_assoc();
            if ($prodotti) {
                foreach ($prodotti as $key => $value) {
                    $prodotti_table->setContent($key, $value);
                }
            }
        } while ($prodotti);
        $table->setContent("sptable", $prodotti_table->get());
        $show->setContent("table", $table->get());
        $main->setContent("content", $show->get());
        $main->close();
    }
}

function accetta_ordine(){
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $numero_ordine= generateRandomString();
    $response = array();
    $mysqli->query("UPDATE tdw_ecommerce.ordini SET stato='MEMORIZZATO', numero_ordine='$numero_ordine' WHERE id=".$id);
    if ($mysqli->affected_rows == 1) {
        $response['success'] = "Prodotto modificato con successo";
    } elseif ($mysqli->affected_rows == 0) {
        $response['warning'] = "Nessun dato modificato";
    } else {
        $response['error'] = "Errore nella modifica del prodotto";
    }
exit(json_encode($response));
}

function edit_stato()
{
    global $mysqli;
    $id = explode('/', $_SERVER['REQUEST_URI'])[3];
    $stato = $_POST["stato"];
    if ($_POST["motivazione"] != "") {
        $motivazione = $_POST["motivazione"];
        if ($stato == "SOSPESO") {
            $mysqli->query("UPDATE tdw_ecommerce.ordini SET stato='$stato', motivazione='$motivazione' WHERE id=".$id);
            $response = array();
            if ($mysqli->affected_rows == 1) {
                $response['success'] = "Stato dell'ordine modificato con successo";
            } elseif ($mysqli->affected_rows == 0) {
                $response['warning'] = "Nessun dato modificato";
            } else {
                $response['error'] = "Errore nella modifica dello stato dell'ordine";
            }
        }if($stato=="ANNULLATO"){
            $mysqli->query("UPDATE tdw_ecommerce.ordini SET stato='$stato', motivazione='$motivazione' WHERE id=".$id);
            $oid= $mysqli-> query("SELECT p.id as id_prodotto, op.quantita as quantita_prodotto FROM tdw_ecommerce.ordini_has_prodotti as op JOIN tdw_ecommerce.prodotti as p on p.id=op.prodotti_id WHERE op.ordini_id=".$id);
            $prodotti = $oid->fetch_assoc();
            if($prodotti){
                do {
                    $mysqli->query("UPDATE tdw_ecommerce.prodotti SET quantita_disponibile=quantita_disponibile+".$prodotti['quantita_prodotto']." WHERE id=".$prodotti['id_prodotto']);
                } while ($prodotti = $oid->fetch_assoc());
            }
        }
    }else{
        $mysqli->query("UPDATE tdw_ecommerce.ordini SET stato='$stato' WHERE id = '$id'");
        $response = array();
        if ($mysqli->affected_rows == 1) {
            $response['success'] = "Stato dell'ordine modificato con successo";
        } elseif ($mysqli->affected_rows == 0) {
            $response['warning'] = "Nessun dato modificato";
        } else {
            $response['error'] = "Errore nella modifica dello stato dell'ordine";
        }
        exit(json_encode($response));
    }
}
function delete()
{
}
